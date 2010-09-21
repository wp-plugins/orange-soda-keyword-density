jQuery.fn.highlight = function (pat) {
    function innerHighlight(node, pat) {
        var skip = 0;
        if (node.nodeType == 3) {
            var pos = node.data.toUpperCase().indexOf(pat);
            if (pos >= 0) {
                var spannode = document.createElement('span');
                spannode.className = 'orangeSoda_highlight';
                var middlebit = node.splitText(pos);
                var endbit = middlebit.splitText(pat.length);
                var middleclone = middlebit.cloneNode(true);
                spannode.appendChild(middleclone);
                middlebit.parentNode.replaceChild(spannode, middlebit);
                skip = 1;
            }
        }
        else if (node.nodeType == 1 && node.childNodes && !/(script|style)/i.test(node.tagName)) {
            for (var i = 0; i < node.childNodes.length; ++i) {
                i += innerHighlight(node.childNodes[i], pat);
            }
        }
        jQuery('#replace').html(jQuery(".orangeSoda_highlight").length);
        return skip;
    }
    return this.each(function () {
        innerHighlight(this, pat.toUpperCase());
    });
};

jQuery.fn.removeHighlight = function () {
    return this.find("span.orangeSoda_highlight").each(function () {
        this.parentNode.firstChild.nodeName;
        with(this.parentNode) {
            replaceChild(this.firstChild, this);
            normalize();
        }
    }).end();
};
		
		jQuery.fn.wordCount = function()
		{
   			
  		//for each keypress function on text areas
 		
    	total_words=jQuery('#content').html().split(/[\s\.\?]+/).length;
   		jQuery('#os_word_counter').html(total_words);
  		
		};

        jQuery(document).ready(function() {
        
        	jQuery('#content').wordCount();
			
			function os_keyword_calculate()
			{
            	var count = 5;
            	var wordCount = jQuery('#os_word_counter').html();
            	wordCount *= .75;
            
            	jQuery.extend(jQuery.wordStats.stopWords, {'retrieved': true, '2007': true});
            
            	jQuery.wordStats.computeTopWords(count, jQuery('#editorcontainer'));

            	var msg = "<h2 style='margin-bottom: 0px;'>Top keywords:</h2><table class='orangesoda_word_table' style='width:100%'><tbody><tr><th>Count</th><th>Density</th><th>Word</th></tr>";
            	for(var i = 0, j = jQuery.wordStats.topWords.length; i < j && i <= count; i++) {
            		var percent = parseFloat((jQuery.wordStats.topWeights[i]/wordCount)*100).toFixed(2);
            		if (percent >= 5){	
            			msg += '<tr style="color: green;"><td> ' + jQuery.wordStats.topWeights[i] + '</td><td> ' + parseFloat((jQuery.wordStats.topWeights[i]/wordCount)*100).toFixed(2) + '% </td><td>'  + jQuery.wordStats.topWords[i].substring(1) + '</td></tr>';
            	    }else {
                	msg += '<tr><td> ' + jQuery.wordStats.topWeights[i] + '</td><td> ' + parseFloat((jQuery.wordStats.topWeights[i]/wordCount)*100).toFixed(2) + '% </td><td>'  + jQuery.wordStats.topWords[i].substring(1) + '</td></tr>';
                	}
            	}
            	msg += '</tbody></table>';
            	jQuery('#os_results').html(msg);

            	jQuery.wordStats.clear();
	        }
	        
	        os_keyword_calculate();
            
            jQuery('#orangeSoda_search_button').live('click', orangeSoda_click);
            function orangeSoda_click(e) 
            {
            	var wordCount = jQuery('#os_word_counter').html();
            	wordCount *= .75;
              var searchPhrase = jQuery('#orangeSoda_search_phrase').val();
              jQuery('#editorcontainer').removeHighlight().highlight(searchPhrase);
              var searchNumber = jQuery('.orangeSoda_highlight').length;
              var message2 = "<table class='orangesoda_word_table' style='border:none; border-style:none; width:100%'><tbody><tr><th>Count</th><th>Density</th><th>Word</th></tr><tr><td>"+searchNumber+"</td><td>"+parseFloat((searchNumber/wordCount)*100).toFixed(1)+"%</td><td>"+searchPhrase+"</td></tbody></table><br /><br />";
              jQuery('#orange_soda_search_density').html(message2);
              jQuery('#editorcontainer').removeHighlight();
            }
        });