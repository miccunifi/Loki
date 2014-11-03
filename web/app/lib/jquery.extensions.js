jQuery.fn.highlight = function (str, className) {
    var regex = new RegExp(str, "gi");
    return this.each(function () {
        this.innerHTML = this.innerHTML.replace(regex, function(matched) {
            return "<span>" + matched + "</span>";
        });
    });
};

jQuery.splitText = function (str, text, thumb) {
    var regex = new RegExp(str, "gi");
	results = [];
    while (m = regex.exec(text)) {
   		var wordsBefore = jQuery.findWordsBefore(text, m.index, thumb);
		var word = jQuery.findSearched(text, m.index + str.length, str);
		var wordsAfter = jQuery.findWordsAfter(text, m.index + str.length, thumb);
		
		//console.log("index: " + m.index + " / before: " + wordsBefore + " / occurrence: " + word + " / after: " + wordsAfter);
		var splittedInstanceHTML = wordsBefore +  word +  wordsAfter;
		results.push(splittedInstanceHTML);
	} 
	return results;
};


jQuery.findWordsBefore = function(str,pos, thumb){
    var words=str.split(' ');
    var offset=0;
    var i;
    for(i=0;i<words.length;i++){
        offset+=words[i].length+1;
        if (offset>pos) break;

    }
	
	var wordsBefore; 
	
	thumb ? wordsBefore = ['<p>', words[i - 10], words[i - 9], words[i - 8], words[i - 7], words[i - 6], words[i - 5], words[i - 4], words[i - 3], words[i - 2], words[i - 1], '</p>'] : wordsBefore = [words[i - 5], words[i - 4], words[i - 3], words[i - 2], words[i - 1]]
    return wordsBefore.join(' ');
}

jQuery.findSearched = function(str,pos, searched){
    var words=str.split(' ');
    var offset=0;
    var i;
    for(i=0;i<words.length;i++){
        offset+=words[i].length+1;
        if (offset>pos) break;

    }
	
	var word = words[i];
	var searchedWord;
	($.trim(searched.toLowerCase()) == $.trim(word.toLowerCase())) ? searchedWord = word : searchedWord = searched;
    return searchedWord;
}


jQuery.findWordsAfter = function(str,pos, thumb){
    var words=str.split(' ');
    var offset=0;
    var i;
    for(i=0;i<words.length;i++){
        offset+=words[i].length+1;
        if (offset>pos) break;

    }
	 var wordsAfter;
	 
	thumb ?  wordsAfter = ['<p>', words[i + 1], words[i + 2], words[i + 3], words[i + 4], words[i + 5], words[i + 6], words[i + 7], words[i + 8], words[i + 9], words[i + 10], '</p>']  : wordsAfter = [words[i + 1], words[i + 2], words[i + 3], words[i + 4], words[i + 5]]
    return  wordsAfter.join(' ');
}

jQuery.subText = function(term, text){
    if(text.length > 750) {
		text = text.substring(0, 750) + "..."
	}
	return text;
}