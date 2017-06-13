var previousmodel = "";
var previousxml = "";

im3iRuntime= new function im3iRuntime() {

    //change this value with the web root of your installation if it's not included in config.js
	this.unifiServer = CFG.absoluteRootPath;


    //other configs
	this.unifiSoapServer = this.unifiServer + 'service/soap_client/server.php';
	this.server = "";
	this.soapServer = this.server + 'webservices/';
	this.proxy = '../php/proxyPlus.php?mimeType=text/xml;charset=UTF-8&url=';
	this.proxyhtml = '../php/proxyPlus.php?mimeType=text/html;charset=UTF-8&url=';
	this.proxytext = '../php/proxyPlus.php?mimeType=text/xml;charset=UTF-8&url=';
	this.unifiProxytext = '../php/proxyPlus.php?mimeType=text/xml;charset=UTF-8&url=';
	this.fileUrl = this.server + 'getfile?id=';
	this.proxyplus = '../php/proxyPlus.php?url=';
	this.proxyupload = '../php/proxyUpload.php?url=';

	this.repository = null;
	this.path = [];
	this.current = null;

	this.setRepository = function (repo) {

		this.repository = repo;
	},

	this.getRepository = function () {

		return this.repository;		
	},
	
	this.restore = function () {
	
		this.repository.restore();
	
	},

	this.getCurrent = function () {
		if (this.current==null) {
			return null;
		} else {
			if(this.current < 0) { this.current = 0 };
			if(this.current >= this.path.length) { this.current = this.path.length - 1 };

			return this.path[this.current];
		}
	},
	
	this.goBackScript = function() {
		if(this.current != 0 && this.current != null) {
			return '<a href="javascript:im3iRuntime.back();im3iRuntime.updateData()">back</a>';
		} else {
		
			return "";
		
		}
	};

	this.goForwardScript = function() {
		if(this.current < (this.path.length-1) && this.current != null) {
			return '<a href="javascript:im3iRuntime.forward();im3iRuntime.updateData()">forward</a>';
		} else {
		
			return "";
		
		}
	};
	
	this.forward = function () {
		if (this.current==null) {
		} else {
			this.current++;
			if(this.current >= this.path.length) { this.current = this.path.length - 1 };
		}
	},
	
	this.addToPath = function (query, rendermodel, classname, hidelist) {
	
		this.path.push({});
		var old = this.current;
		this.current = this.path.length - 1;

		this.path[this.current].query = query;


		this.path[this.current].rendermodel = rendermodel;
		if(classname == "") {
	
			this.path[this.current].classname = this.path[old].classname;
	
		} else {

			this.path[this.current].classname = classname;
		
		}
		this.path[this.current].hidelist = hidelist;
	},

	this.createObject = function (datamodel, linkmodel, target, hide) {
		
		if(datamodel != '') {

			var type = this.repository.getDatamodel(datamodel)

			if(type != null) {
				var obj = this.repository.createObject(type.getId())
	
				if(obj != null ) {
		
					if(linkmodel == "" ) {
					
						this.showCurrentModel()
	
					} else {
					
						this.showNewModelByObject(obj.getId(), linkmodel, target, hide)
			
					}
				}
			}
		}
	
	}

	this.addToUserModel = function(datamodel, elementmodel, newvalue, linkmodel, target, hide) {

		var dataobject = this.repository.getUserObject(datamodel, im3iQS.getUserId())
		newvalue = this.repository.dataobjects.get(newvalue);
		
		if(dataobject != null && newvalue != null) {

			this.repository.addToValue(dataobject.getId(), elementmodel, newvalue)
			this.repository.save()

			if(linkmodel == "" ) {
			
				this.showCurrentModel()
	
			} else {
			
				this.showNewModelByObject(dataobject.getId(), linkmodel, target, hide)
	
			}
		}

	}


	this.showRemoteObject = function(sourceText, id, eid, pos, rendermodel, service, im, refresh) {

		var inputmodel = this.repository.inputmodels.find('title', im)
		var object = this.repository.dataobjects.get(id)

		if(inputmodel.length > 0 && rendermodel != null) {

			inputmodel[0].remoteCall(sourceText, object, eid, pos, rendermodel, service, refresh);
			
		}

	}

	this.addObjectToUserModel = function(datamodel, elementmodel, newmodel, newelement, newvalue, linkmodel, target, hide) {

		newvalue = this.repository.dataobjects.get(newvalue);
		var dataobject = this.repository.createObject(newmodel)
		
		if(dataobject != null && newvalue != null) {
			this.repository.addToValue(dataobject.getId(), newelement, newvalue)

			this.addToUserModel(datamodel, elementmodel, dataobject.getId(), linkmodel, target, hide)
					
		}

	}

	this.assignObject = function (addedObj, addedObjModel, containerObj, containerObjModel) {
	
		var dataobject = this.repository.dataobjects.get(addedObj);
//		var container = this.repository.dataobjects.get(containerObj);
		var datamodel = this.repository.datamodels.get(containerObjModel)

		var objecttype = repository.getType('object');
		var elements = datamodel.getElementsByType(objecttype);
		var foundElement = null;
		for(var i in elements) {
		
			if(elements[i].getObjectDataModel().getId() == addedObjModel ) {
				foundElement = elements[i]
			}
		}

		if(foundElement != null) {

			this.repository.addToValue(containerObj, foundElement.getId(), dataobject)			
			this.showCurrentModel()
		}

	}


	this.removeAssignment = function (removedObj, removedObjModel, containerObj, containerObjModel) {
	
		var dataobject = this.repository.dataobjects.get(removedObj);
		var container = this.repository.dataobjects.get(containerObj);
		var datamodel = this.repository.datamodels.get(containerObjModel)

		var objecttype = repository.getType('object');
		var elements = datamodel.getElementsByType(objecttype);
		var foundElement = null;
		for(var i in elements) {
		
			if(elements[i].getObjectDataModel().getId() == removedObjModel ) {
				foundElement = elements[i]
			}
		}
		
		

		if(foundElement != null) {

			var position = container.getDataElementPosition(foundElement, dataobject)

			this.repository.deleteValue(containerObj, foundElement.getId(), position)
			this.showCurrentModel()
		}

	}

	this.addObject = function (idtag, type, linkmodel, target, hide) {
	
		var obj = this.repository.createObject(type)
		
		if(obj != null ) {
			this.updateDataCore(idtag, obj)
			this.repository.save()

			if(linkmodel == "" ) {
			
				this.showCurrentModel()
	
			} else {
			
				this.showNewModelByObject(obj.getId(), linkmodel, target, hide)
	
			}
		}
	
	}

	this.updateData = function(formid) {

		var repository = this.repository;
		var obj = this;
		var deleteList = [];
		
		
		$('.filter_text').each(function()
			{

				obj.updateQSParameters(this.name, this.value)
			}
		)

		$('.filter_select').each(function(i, selected)
			{

				obj.updateQSParameters(this.id, this.value)
			}
		)


		
		$('#' + formid + ' textarea').each(function()
			{
				obj.updateDataCore(this.id, this.value)
			}
		)
		

		$('#' + formid + ' input').each(function()
			{
				obj.updateDataCore(this.id, this.value)
			}
		)
		
		$('#' + formid + ' select').each(function(i, selected)
			{
				if(this.value == "") {
				
					if(this.id.indexOf('update') > -1) {
						deleteList.push(this.id.replace('update', 'del'))
					}
					
				} else {

					obj.updateDataCore(this.id, this.value)
				}
			}
		)
		$('#' + formid + ' :checkbox').each(function(i, selected)
			{
				if(this.checked) {
				
					deleteList.push(this.name)
				}
			}
		)
		
		deleteList.sort()
		var del_object = -1;
		var del_index = 0;
		var last_index = -1;

		for(var index in deleteList) {
		
			var id = deleteList[index].split('_')

			if (id.length == 4) {
			
				var objectid = toInt(id[1]);
				var elementid = id[2];
				var index = id[3] -1

				if(del_object == objectid) {
					del_index++;
				} else {
					del_index = 0;
					del_object = objectid;
					last_index = -1;
				}

				if(last_index == index) {
					//skip and correct del_index counter
					del_index--
				} else {
					this.repository.deleteValue(objectid, elementid, (index - del_index))
					last_index = index;
				}

			} else if (id.length = 2) {

				var objectid = toInt(id[1]);
				
				this.repository.deleteObject(objectid)
			}
		}

		im3iQS.setParameter('action', '1');

		this.repository.save()

		this.showCurrentModel()
	},


	this.updateDataCore = function(idstr, value) {
	
		var id = idstr.split('_');
		
		var newvalue = value;

		if (id.length > 0) {
		
			var type = id[0]

			if (type == 'update') {
							
				if (id.length == 4) {
				
					var objectid = id[1];
					var elementid = id[2];
					var index = id[3] -1

					this.repository.updateValue(objectid, elementid, index, newvalue)
				
				}

			} else if (type == 'new') {

				if (id.length == 3) {

					var objectid = id[1];
					var elementid = id[2];
					
					this.repository.addToValue(objectid, elementid, newvalue)
				
				}
			}
		}
	},

	this.firstpage = function(currentpage, maxpage) {
		if(currentpage != 1) {
			im3iQS.setParameter('pagenumber', 1);
			this.showCurrentModel()
		}
	},

	this.previouspage = function(currentpage, maxpage) {
		if(currentpage > 1) {
			im3iQS.setParameter('pagenumber', currentpage - 1);
			this.showCurrentModel()
		}
	},

	this.nextpage = function(currentpage, maxpage) {
		if(currentpage < maxpage) {
			im3iQS.setParameter('pagenumber', currentpage + 1);
			this.showCurrentModel()
		}
	},

	this.lastpage = function(currentpage, maxpage) {
		if(currentpage != maxpage) {
			im3iQS.setParameter('pagenumber', maxpage);
			this.showCurrentModel()
		}
	},

	this.resetQSParameters = function() {

		im3iQS.setParameter('pagenumber', 1);

/*
		var current = this.getCurrent();

		if(current != null) {

			var rendermodel = this.repository.getRenderModelObject(current.query, current.rendermodel);
			var allFilters = rendermodel.getQSStoreList();
			
			for(var index in allFilters) {
			
				if(allFilters[index] && allFilters[index].defaut != undefined) {
	
//					im3iQS.setParameter(allFilters[index].id, allFilters[index].default);
				
				}
			}
		}
*/	
	},

	this.updateQSParameters = function(label, value) {

		im3iQS.setParameter(label, value);
		
	},

	this.showCurrentModel = function() {

		var current = this.getCurrent();

		if(current.query instanceof DataObject) {
		
			im3iQS.setParameter('im3iid', current.query.getId());
		
		} else {
			im3iQS.setParameter('im3iid', '');
		
		}

		var renderxml = this.repository.getRenderXML(current.query, current.rendermodel);

		var rendermodel = this.repository.getRenderModel(current.query, current.rendermodel);

		if(renderxml == "" || rendermodel == "") {
		
			debug('Renderxml: '  + renderxml.length + ', rendermodel: ' + rendermodel.length)
			
		} else {

			$(current.classname).xslt(renderxml, rendermodel);
			
			if(current.classname == '#im3iOverlayTarget' ) {
			
				this.showOverlay()
			
			} else {

				this.hideOverlay()
			
			}
		
		}

		if(current.hidelist != "") {
			$(current.hidelist).hide();
		};

		var uploadlist = [];
		var fancylist = [];
		
		$('button').each(function() {
		
			var idstr = toString(this.id)

			if(idstr.startsWith("new_") || idstr.startsWith("update_")) {
				if ($(this).hasClass('fancyupload')) {
			 		fancylist.push(idstr);
			 	} else {
			 		uploadlist.push(idstr);
			 	}
			}
		});
		
		var obj = this;
		
		for(var index in uploadlist) {
		 	var uploads = new AjaxUpload(uploadlist[index], {
						action: this.proxyupload + this.server + 'fancyupload',
						responseType: 'json',
						propertyName: 'userfile',
						data: {'doid': obj.getIdFromTag(uploadlist[index])},
						onComplete: function (file, response) {

							if (response.status == 1) {
								obj.updateDataCore(this._button.id, 
											"<name>" + file + "</name><sep>_</sep><id>" + response.id + "</id>")

								var current = obj.getCurrent()
								var renderobject = obj.repository.getRenderModelObject(current.query,current.rendermodel)
								obj.updateData('rm' + renderobject.getId())

								obj.showCurrentModel()
								obj.repository.save()

							} else {
							
								alert('Uploading the file "' + file + '" failed.')
							
							}
						}
			});
		}
		
		for (var index in fancylist) {
			$('#' + fancylist[index])
			.after('<input type="button" value="Start Uploading" onclick="$(\'#' + fancylist[index] +'\').uploadifyUpload();" />')
			.uploadify({
				'uploader': '/im3i/lib/uploadify/uploadify.swf',
				'cancelImg': '/im3i/lib/uploadify/cancel.png',
				'script': this.server + 'fancyupload',
				'fileDataName': 'userfile',
				'multi': true,
				'buttonText': 'Browse',
				'displayData': 'speed',
				'simUploadLimit': 1,
				'scriptData': {'doid': obj.getIdFromTag(fancylist[index])},
				onComplete: function(event, ID, fileObj, response, data) {
					var file = fileObj.name;
					response = eval(response);
					
					if (response.status == 1) {
						
						obj.updateDataCore(this._button.id, 
									"<name>" + file + "</name><sep>_</sep><id>" + response.id + "</id>")

						var current = obj.getCurrent()
						var renderobject = obj.repository.getRenderModelObject(current.query,current.rendermodel)
						obj.updateData('rm' + renderobject.getId())

						obj.showCurrentModel()
						obj.repository.save()

					} else {
					
						alert('Uploading the file "' + file + '" failed.')
					
					}
				}
			});
		}

if (typeof($.fn.autocomplete) == 'function') 
{
		var userautocomplete = []
		$('input[id^=user_]').autocomplete({
			source: function( request, response ) {
			
				$.soapRequest({
					url: obj.proxy + obj.soapServer + 'mediaforweb/',
					method: 'med:getUsersForAutoComplete',
					params: {
						nameprefix: request.term
					},
					returnJson: false,
					success: function(data) {

						response( $.map( eval(data.documentElement.textContent), function( item ) {
							return {
								id: item.id,
								label: item.label,
								value: item.value
							}
						}));
					},
					error: function(string) {
						debug(string);
					}
				});
			},
			minLength: 2,
			select: function( event, ui ) {
				debug( ui.item ?
					"Selected: " + ui.item.value + " aka " + ui.item.id :
					"Nothing selected, input was " + this.value );
			}
		});
}

	
//		this.repository.redraw();

	},

	this.getIdFromTag = function(tag) {
		taglist = tag.split('_')
		return taglist[1];
	},
	
	this.behaviors = ['imagegallery', 'dock', 'spacegallery', 'triptych'];
	
	this.startBehaviors = function(element) {
		for (var i in behaviors) {
			if ($('.' + behaviors[i]).length > 0) {
				im3i.include(behaviors[i],'$("#' + element + '").' + behaviors[i] + '()');
			}
		}
	}

	this.showNewModelPrep = function(query, rendermodel, classname, hidelist) {
		var rendermodel = this.repository.getRenderModelObject(query, rendermodel)		

		this.addToPath(query, rendermodel, classname, hidelist)

		this.resetQSParameters();
	},
	
	this.showNewModel = function(query, rendermodel, classname, hidelist) {
		this.showNewModelPrep(query, rendermodel, classname, hidelist);

		this.showCurrentModel()
	},

	this.showNewModelByObjectPrep = function(queryobj, rendermodel, classname, hidelist) {
		var query = this.repository.dataobjects.get(queryobj)

		var model = this.repository.getRenderModelObject(query, rendermodel)

		if(model == null) {

		} else {

			this.addToPath(query, model, classname, hidelist)
	
			this.resetQSParameters();
		}
	},
	
	this.showNewModelByObject = function(queryobj, rendermodel, classname, hidelist) {

		this.showNewModelByObjectPrep(queryobj, rendermodel, classname, hidelist)
		this.showCurrentModel();
	},
	
	this.login = function() {
	
		var username = $('#im3i_username')[0].value
		var password = $('#im3i_password')[0].value
		
		im3iSoapCalls.login(username, password, this.handleLogin, this);

	},
	
	this.handleLogin = function (sessionid, userid, userlogin, baseobj) {

		if (!isNaN(toInt(userid))) {

			$.cookie('sessionid', sessionid);
			$.cookie('userid', userid);
			$.cookie('userlogin', userlogin);
		}
		baseobj.showCurrentModel()
	},
	
	this.logout = function() {

		$.cookie('sessionid', '', { expires: -1 });
		$.cookie('userid', '', { expires: -1 });
		$.cookie('userlogin', '', { expires: -1 });	

		this.showCurrentModel()
	}
	
	this.createOverlay = function() {
		if ($('div#im3iOverlay').length == 0) {
			$('body').append('<div id="im3iOverlay" class="hidden"><div id="im3iOverlayStage"><a id="im3iOverlayClose" href="Javascript:im3iRuntime.hideOverlay();">[close]</a><div id="im3iOverlayTarget"></div></div></div>')
		}
		$(window).resize(function() {
			this.sizeOverlay();
		});
    
	}
	
	this.showOverlay = function() {
		$('div#im3iOverlay')
			.removeClass('hidden')
			.click(function() {
				$(this).addClass('hidden');
			});
		this.sizeOverlay();
	}
	
	this.sizeOverlay = function() {
		$('div#im3iOverlay')
	    .css({
	    	width: '100%', 
	      height: '100%' 
	    });
	  var myLeft = ($(window).width() - $('div#im3iOverlayStage').width()) / 2;
	  var myTop = ($(window).height() - $('div#im3iOverlayStage').height()) / 2;
	  $('div#im3iOverlayStage')
	    .css({
	    	left: myLeft,
	      top: myTop
	    })
	    .click(function(event) {
	    	event.stopPropagation();
	    });
	}
	
	this.hideOverlay = function() {
		$('div#im3iOverlay').addClass('hidden');
	}
	
	im3iQS = new function im3iQS() {
	
		this.param = {}

		this.getUserId = function() {
		
			var uid = $.cookie('userid');
			if(uid == null) {
			
				return ''

			} else{
			
				return toInt(uid);
			
			}
		},

		this.getUserLogin = function() {
		
			var ulogin = $.cookie('userlogin');
			if(ulogin == null) {
			
				return ''

			} else{
			
				return ulogin;
			
			}
		},

		this.getQSParameterXML = function () {
	
			// always set the most recent userid

			this.setParameter('userid', this.getUserId())
			this.setParameter('userlogin', this.getUserLogin())
			
		
			xml = "<param>"

			for(var index in this.param) {

				if(toString(this.param[index]) != "" ) {

					xml = xml + "<" + index + ">"
					xml = xml + this.param[index]
					xml = xml + "</" + index + ">"

				}
			}
			xml = xml + "</param>"

			return xml
		},
		
		this.setParameter = function(label, value) {

			if(value == undefined) {
				this.param[label] = '';
			} else {			
				this.param[label] = value;
			}
		},

		this.getParameterXSL = function(label) {
		
			return '/param/' + label;
	
		},

		this.getParameterValue = function(label) {
		
			return this.param[label]
	
		}
	}	
}