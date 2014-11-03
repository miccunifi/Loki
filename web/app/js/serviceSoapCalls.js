/*
Minimal function set to call im3i repository for getExtendedSources
Original code in repository/DataRepository.js
 */

im3iSoapCalls = {
	soapServer : im3iRuntime.soapServer,
	unifiSoapServer : im3iRuntime.unifiSoapServer,
	proxy : im3iRuntime.proxytext,
	unifiProxy : im3iRuntime.unifiProxytext,

	userData : {
		'sessionid' : -1,
		'userid' : -1,
		'userlogin' : -1
	},

	lastXhrRefImages : null,

	login : function(login, password, returnFunction, baseobj, arg1, arg2,
			arg3, arg4) {

		$.soapRequest({
			url : this.proxy + this.soapServer + 'media/',
			method : 'doLogin',
			params : {
				login : login,
				password : password
			},
			returnJson : false,
			success : function(data) {

				if (typeof (returnFunction) == 'function') {

					var sessionid = $(data).find("id").text();
					var userid = $(data).find("userId").text();
					var userlogin = $(data).find("login").text();

					returnFunction(sessionid, userid, userlogin, baseobj, arg1,
							arg2, arg3, arg4);
				}

			},
			error : function(string) {

				debug(string);

			}
		});
	},

	getExtendedSources : function(id, returnFunction) {
		$.soapRequest({
			url : im3iSoapCalls.proxy + im3iSoapCalls.soapServer
					+ 'mediaforweb/',
			method : 'med:getExtendedSources',
			params : {
				sourceId : id
			},
			returnJson : false,
			success : function(data) {

				if (typeof (returnFunction) == 'function') {
					returnFunction(data);
				}
			},
			error : function(string) {
				debug(string);
			}
		});
	},

	doLogin : function(user, pwd, soapServer, returnFunction, errorFunction) {
		$.soapRequest({
			url : (soapServer == 'unifi' ? im3iSoapCalls.unifiProxy
					+ im3iSoapCalls.unifiSoapServer : im3iSoapCalls.proxy
					+ im3iSoapCalls.soapServer + 'mediaforweb/'),
			method : (soapServer == 'unifi' ? 'urn:doLogin' : 'ser:doLogin'),
			params : {
				login : user,
				password : pwd
			},
			returnJson : false,
			serviceOwner : soapServer,
			success : function(data) {

				if (typeof (returnFunction) == 'function') {
					returnFunction(data);
				}
			},
			error : function(data) {
				if (typeof (errorFunction) == 'function') {
					errorFunction(data);
				}
			}
		});
	},

	getSourceId : function(session, source, soapServer, returnFunction,
			errorFunction) {

		if (soapServer == 'unifi') {
			paramsObject = {
				'sessionId' : session,
				'sourceId' : source
			};
		} else {
			paramsObject = {

				'sourceId' : source,
				'sessionId' : session,
				'schemaName' : 'DocumentPages'
			};
		}

		$.soapRequest({
			url : (soapServer == 'unifi' ? im3iSoapCalls.unifiProxy
					+ im3iSoapCalls.unifiSoapServer : im3iSoapCalls.proxy
					+ im3iSoapCalls.soapServer + 'mediaforweb/'),
			method : (soapServer == 'unifi' ? 'urn:getSourceIdDetailRequest'
					: 'ser:getArtifactForSource'),
			params : paramsObject,
			returnJson : false,
			serviceOwner : soapServer,
			success : function(data) {

				if (typeof (returnFunction) == 'function') {
					returnFunction(data);
				}
			},
			error : function(data) {
				if (typeof (errorFunction) == 'function') {
					errorFunction(data);
				}
			}
		});
	},

	getAnnotationImage : function(id, session, time, duration, soapServer,
			returnFunction, errorFunction) {
		$
				.soapRequest({
					url : (soapServer == 'unifi' ? im3iSoapCalls.unifiProxy
							+ im3iSoapCalls.unifiSoapServer
							: im3iSoapCalls.proxy + im3iSoapCalls.soapServer
									+ 'mediaforweb/'),
					method : (soapServer == 'unifi' ? 'urn:getAnnotationsForRangeRequest'
							: 'ser:getAnnotationsForRange'),
					params : {
						sessionId : session,
						sourceId : id,
						timePoint : time + "",
						duration : duration
					},
					returnJson : false,
					serviceOwner : soapServer,
					success : function(data) {

						if (typeof (returnFunction) == 'function') {
							returnFunction(data);
						}
					},
					error : function(data) {
						if (typeof (errorFunction) == 'function') {
							errorFunction(data);
						}
					}
				});
	},

	getAnnotationDocuments : function(id, session, soapServer, page, queryType,
			returnFunction, errorFunction) {

		var par;

		if (soapServer == 'unifi') {
			par = {
				sessionId : session,
				sourceId : id,
				page : page,
				queryType : queryType
			};

		} else {
			par = "";
		}

		$
				.soapRequest({
					url : (soapServer == 'unifi' ? im3iSoapCalls.unifiProxy
							+ im3iSoapCalls.unifiSoapServer
							: im3iSoapCalls.proxy + im3iSoapCalls.soapServer
									+ 'mediaforweb/'),
					method : (soapServer == 'unifi' ? 'urn:getAnnotationsForRangeRequest'
							: 'ser:getAnnotationsForRange'),
					params : par,
					returnJson : false,
					serviceOwner : soapServer,
					success : function(data) {

						if (typeof (returnFunction) == 'function') {
							returnFunction(data);
						}
					},
					error : function(data) {
						if (typeof (errorFunction) == 'function') {
							errorFunction(data);
						}
					}
				});
	},

	mpeg7GetAnnotation : function(id, session, time, duration, soapServer,
			returnFunction, errorFunction) {
		$
				.soapRequest({
					url : (soapServer == 'unifi' ? im3iSoapCalls.unifiProxy
							+ im3iSoapCalls.unifiSoapServer
							: im3iSoapCalls.proxy + im3iSoapCalls.soapServer
									+ 'mediaforweb/'),
					method : (soapServer == 'unifi' ? 'urn:getAnnotationsForRangeRequest'
							: 'ser:getAnnotationsForRange'),
					params : {
						sessionId : session,
						sourceId : id,
						timePoint : time + "",
						duration : duration
					},
					returnJson : false,
					serviceOwner : soapServer,
					success : function(data) {

						if (typeof (returnFunction) == 'function') {
							returnFunction(data);
						}
					},
					error : function(data) {
						if (typeof (errorFunction) == 'function') {
							errorFunction(data);
						}
					}
				});
	},

	mpeg7GetTranscription : function(id, session, time, duration, soapServer,
			returnFunction, errorFunction) {
		$
				.soapRequest({
					url : (soapServer == 'unifi' ? im3iSoapCalls.unifiProxy
							+ im3iSoapCalls.unifiSoapServer
							: im3iSoapCalls.proxy + im3iSoapCalls.soapServer
									+ 'mediaforweb/'),
					method : (soapServer == 'unifi' ? 'urn:getTranscriptionsForRangeRequest'
							: 'ser:getTranscriptionsForRange'),
					params : {
						sessionId : session,
						sourceId : id,
						timePoint : time + "",
						duration : duration
					},
					returnJson : false,
					serviceOwner : soapServer,
					success : function(data) {

						if (typeof (returnFunction) == 'function') {
							returnFunction(data);
						}
					},
					error : function(data) {
						if (typeof (errorFunction) == 'function') {
							errorFunction(data);
						}
					}
				});
	},
	
	addAnnotationVideoAudio : function(agent, comment, sessionId, confidence, soapServer,
			type, annotations, returnFunction, errorFunction) {

		var paramsObject;

		if (soapServer == 'unifi') {
			/*
			 * paramsObject = { 'sessionId': sessionId, 'sourceId': source,
			 * 'timePoint': start, 'duration': duration, 'keyword': text,
			 * 'owner': '1' }
			 */

			/*
			 * paramsObject = {'annotations': { 'sessionId': sessionId,
			 * 'annotation': [{ 'sourceId': '1001', 'timePoint': start,
			 * 'duration': duration, 'keyword': text, 'owner': '1' }, {
			 * 'sourceId': '1002', 'timePoint': start, 'duration': duration,
			 * 'keyword': text, 'owner': '1' }] } }
			 */
			var annotationObj = {
				'sessionId' : sessionId,
				'annotations' : {
					'annotation' : []
				}
			};

			for (var i = 0; i < annotations.length; i++) {
				annotationObj.annotations.annotation.push(annotations[i]);
			}

			paramsObject = annotationObj;

		} else {

			var source = annotations[0].sourceId;
			var start = annotations[0].timePoint;
			var text = annotations[0].keyword;
			var duration = annotations[0].duration;

			paramsObject = {

				'sessionId' : sessionId,
				'annotation' : {
					'sourceId' : source,
					'start' : start.toString(),
					'type' : type,
					'keyword' : text,
					'agent' : agent,
					'comment' : comment,
					'confidence' : confidence,
					'owner' : soapServer,
					'duration' : duration
				}
			};
			}

		$.soapRequest({
				url : (soapServer == 'unifi' ? im3iSoapCalls.unifiProxy
						+ im3iSoapCalls.unifiSoapServer : im3iSoapCalls.proxy
						+ im3iSoapCalls.soapServer + 'mediaforweb/'),
				method : (soapServer == 'unifi' ? 'urn:setAnnotationsRequest'
						: 'ser:addAnnotation'),
				params : paramsObject,
				returnJson : false,
				serviceOwner : soapServer,
				success : function(data) {

                    solr.index();
	
					if (typeof (returnFunction) == 'function') {
						returnFunction(data);
					}
				},
				error : function(data) {
					if (typeof (errorFunction) == 'function') {
						errorFunction(data);
					}
				}
		});

	},
	
	addAnnotationImage : function(agent, comment, sessionId, confidence,
			soapServer, type, annotations, returnFunction, errorFunction) {

		
		var paramsObject;

		if (soapServer == 'unifi') {
			var annotationObj = {
				'sessionId' : sessionId,
				'annotations' : {
					'annotation' : []
				}
			};

			for (var i = 0; i < annotations.length; i++) {
//				console.log(annotations[i]);
				annotationObj.annotations.annotation.push(annotations[i]);
			}

			paramsObject = annotationObj;

		} else {

			var source = annotations[0].sourceId;
			var start = annotations[0].timePoint;
			var text = annotations[0].keyword;
			var duration = annotations[0].duration;

			paramsObject = {

				'sessionId' : sessionId,
				'annotation' : {
					'sourceId' : source,
					'start' : start.toString(),
					'type' : type,
					'keyword' : text,
					'agent' : agent,
					'comment' : comment,
					'confidence' : confidence,
					'owner' : soapServer,
					'duration' : duration
				}
			};
		}

		$.soapRequest({
			url : (soapServer == 'unifi' ? im3iSoapCalls.unifiProxy
					+ im3iSoapCalls.unifiSoapServer : im3iSoapCalls.proxy
					+ im3iSoapCalls.soapServer + 'mediaforweb/'),
			method : (soapServer == 'unifi' ? 'urn:setAnnotationsRequest'
					: 'ser:addAnnotation'),
			params : paramsObject,
			returnJson : false,
			serviceOwner : soapServer,
			success : function(data) {

                solr.index();

				if (typeof (returnFunction) == 'function') {
					returnFunction(data);
				}
			},
			error : function(data) {
				if (typeof (errorFunction) == 'function') {
					errorFunction(data);
				}
			}
		});

	},

	addAnnotationDocument : function(agent, comment, sessionId, confidence,
			soapServer, owner, type, annotations, returnFunction, errorFunction) {

		var paramsObject;

		if (soapServer == 'unifi') {

			var annotationObj = {
				'sessionId' : sessionId,
				'annotations' : {
					'annotation' : []
				}
			};

			for (var i = 0; i < annotations.length; i++) {
				annotations[i].confidence = confidence;

				annotationObj.annotations.annotation.push(annotations[i]);
			}

			paramsObject = annotationObj;

		} else {

			var source = annotations[0].sourceId;
			var start = annotations[0].timePoint;
			var text = annotations[0].keyword;
			var duration = annotations[0].duration;

			paramsObject = {

				'sessionId' : sessionId,
				'annotation' : {
					'sourceId' : source,
					'start' : start.toString(),
					'type' : type,
					'keyword' : text,
					'agent' : agent,
					'comment' : comment,
					'confidence' : confidence,
					'owner' : owner,
					'duration' : duration
				}
			};
		}

		$.soapRequest({
			url : (soapServer == 'unifi' ? im3iSoapCalls.unifiProxy
					+ im3iSoapCalls.unifiSoapServer : im3iSoapCalls.proxy
					+ im3iSoapCalls.soapServer + 'mediaforweb/'),
			method : (soapServer == 'unifi' ? 'urn:setAnnotationsRequest'
					: 'ser:addAnnotation'),
			params : paramsObject,
			returnJson : false,
			serviceOwner : soapServer,
			success : function(data) {

                solr.index();

				if (typeof (returnFunction) == 'function') {
					returnFunction(data);
				}
			},
			error : function(data) {
				if (typeof (errorFunction) == 'function') {
					errorFunction(data);
				}
			}
		});

	},

	updateAnnotation : function(agent, comment, sessionId, confidence,
			soapServer, type, annotations, returnFunction, errorFunction) {

		var paramsObject;

		if (soapServer == 'unifi') {

			var annotationObj = {
				'sessionId' : sessionId,
				'annotations' : {
					'annotation' : []
				}
			};

			for (var i = 0; i < annotations.length; i++) {
				annotationObj.annotations.annotation.push(annotations[i]);
			}

			paramsObject = annotationObj;

		} else {

			var source = annotations[0].sourceId;
			var start = annotations[0].timePoint;
			var text = annotations[0].keyword;
			var duration = annotations[0].duration;

			paramsObject = {

				'sessionId' : sessionId,
				'annotation' : {
					'sourceId' : source,
					'start' : start.toString(),
					'type' : type,
					'keyword' : text,
					'agent' : agent,
					'comment' : comment,
					'confidence' : confidence,
					'owner' : soapServer,
					'duration' : duration
				}
			};
		}

		$.soapRequest({
			url : (soapServer == 'unifi' ? im3iSoapCalls.unifiProxy
					+ im3iSoapCalls.unifiSoapServer : im3iSoapCalls.proxy
					+ im3iSoapCalls.soapServer + 'mediaforweb/'),
			method : (soapServer == 'unifi' ? 'urn:setAnnotationsRequest'
					: 'ser:addAnnotation'),
			params : paramsObject,
			returnJson : false,
			serviceOwner : soapServer,
			success : function(data) {

				if (typeof (returnFunction) == 'function') {
					returnFunction(data);
				}
			},
			error : function(data) {
				if (typeof (errorFunction) == 'function') {
					errorFunction(data);
				}
			}
		});
	},

	searchAnnotations : function(id, session, keyword, page, recPerPage,
			interval, soapServer, returnFunction, errorFunction) {

		if (soapServer == 'unifi') {
			paramsObject = {
				sessionId : session,
				sourceId : id,
				keyword : keyword,
				page : page,
				record_per_page : recPerPage,
				interval : interval
			};
		} else {
			paramsObject = {
				sessionId : session,
				sourceId : id,
				keyword : keyword,
				page : page,
				pagesize : recPerPage,
				interval : interval
			};
		}

		$.soapRequest({
			url : (soapServer == 'unifi' ? im3iSoapCalls.unifiProxy
					+ im3iSoapCalls.unifiSoapServer : im3iSoapCalls.proxy
					+ im3iSoapCalls.soapServer + 'mediaforweb/'),
			method : (soapServer == 'unifi' ? 'urn:getAnnotationsSearchRequest'
					: 'ser:searchAnnotationsForKeyword'),
			params : paramsObject,
			returnJson : false,
			serviceOwner : soapServer,
			success : function(data) {

				if (typeof (returnFunction) == 'function') {
					returnFunction(data);
				}
			},
			error : function(data) {
				if (typeof (errorFunction) == 'function') {
					errorFunction(data);
				}
			}
		});
	},

	suggestAnnotation : function(session, source, keyword, soapServer,
			returnFunction, errorFunction) {
		$.soapRequest({
			url : (soapServer == 'unifi' ? im3iSoapCalls.unifiProxy
					+ im3iSoapCalls.unifiSoapServer : im3iSoapCalls.proxy
					+ im3iSoapCalls.soapServer + 'mediaforweb/'),
			method : (soapServer == 'unifi' ? 'urn:getAutoCompleteRequest'
					: 'ser:getAutoCompleteAnnotations'),
			params : {
				sessionId : session,
				sourceId : source,
				keyword : keyword
			},
			returnJson : false,
			serviceOwner : soapServer,
			success : function(data) {

				if (typeof (returnFunction) == 'function') {
					returnFunction(data);
				}
			},
			error : function(data) {
				if (typeof (errorFunction) == 'function') {
					errorFunction(data);
				}
			}
		});
	},

	deleteAnnotation : function(id, session, soapServer, returnFunction,
			errorFunction) {

		var paramsObject;

		if (soapServer == 'unifi') {
			paramsObject = {
				id : id,
				owner : soapServer
			};
		} else {
			paramsObject = {
				sessionId : session,
				annotationId : id
			};
		}

		$.soapRequest({
			url : (soapServer == 'unifi' ? im3iSoapCalls.unifiProxy
					+ im3iSoapCalls.unifiSoapServer : im3iSoapCalls.proxy
					+ im3iSoapCalls.soapServer + 'mediaforweb/'),
			method : (soapServer == 'unifi' ? 'urn:delAnnotationsRequest'
					: 'ser:deleteAnnotation'),
			params : paramsObject,
			returnJson : false,
			serviceOwner : soapServer,
			success : function(data) {

				if (typeof (returnFunction) == 'function') {
					returnFunction(data);
				}
			},
			error : function(data) {
				if (typeof (errorFunction) == 'function') {
					errorFunction(data);
				}
			}
		});
		solr.index();
	},

	findSimilarImages : function(sessionId, sourceId, timepoint,
			useExactTimepoint, find_all, page, recPerPage, soapServer,
			returnFunction, errorFunction) {

		var paramsObject;

		if (soapServer == 'unifi') {
			paramsObject = {
				'sessionId' : sessionId,
				'sourceId' : sourceId,
				'timePoint' : timepoint.toString(),
				'exact_timepoint' : useExactTimepoint,
				'find_all' : find_all,
				'page' : page,
				'record_per_page' : recPerPage
			};
		} else {
			paramsObject = null;
		}

		if (this.lastXhrRefImages != null)
			this.lastXhrRefImages.abort();

		this.lastXhrRefImages = $.soapRequest({
			url : (soapServer == 'unifi' ? im3iSoapCalls.unifiProxy
					+ im3iSoapCalls.unifiSoapServer : im3iSoapCalls.proxy
					+ im3iSoapCalls.soapServer + 'mediaforweb/'),
			method : (soapServer == 'unifi' ? 'urn:getSimilarImages' : ' '),
			params : paramsObject,
			returnJson : false,
			serviceOwner : soapServer,
			success : function(data) {

				if (typeof (returnFunction) == 'function') {
					returnFunction(data);
				}
			},
			error : function(data) {
				if (typeof (errorFunction) == 'function') {
					errorFunction(data);
				}
			}
		});
	}

};
