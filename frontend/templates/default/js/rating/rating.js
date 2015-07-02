function Rating(){
    this.ratingSprite = '/templates/default/images/png/rating/stars.png';
    this.ratingPointSize = {width:15, height:15}; // The rating star(or anything else) width and height
	this.ratesCurrentWidth = {}; // Current rating block width
	this.ratingBlock = 'star';   // Ratings block class name
    this.ratingContainer = "js-rating";  // Rating block container class name
    this.localization = 'Рейтинг'; // Word that means rating in your local
    this.userVoteInfo = {}; //Information about user votes for each element on page
	var _this = this;  // For namespace vision inside function passed as argument



    this.initialize = function(){

        var ratingViews = document.getElementsByClassName(this.ratingContainer); //Detect all rating blocks on page

        if(ratingViews) {
            userVotes(sortTargets(ratingViews));

            for (var i = 0; i < ratingViews.length; i++) {
                this.showRating(ratingViews[i]);
            }
        }

    };
	
	
	// Shows rating for passed element
	this.showRating = function(ratingContainer){

        var target = ratingContainer.getAttribute('data-target');
        var targetId = Number(ratingContainer.getAttribute('data-target-id'));
        var totalVoters  = 0;
        var totalRate = 0;


        if(target in this.userVoteInfo && targetId in this.userVoteInfo[target]){
            totalVoters = this.userVoteInfo[target][targetId]['totalVoters'] != undefined ? Number(this.userVoteInfo[target][targetId]['totalVoters']) : 0;
            totalRate = this.userVoteInfo[target][targetId]['rating'] != undefined ? Number(this.userVoteInfo[target][targetId]['rating']) : 0;
        }

		var rating = totalRate / totalVoters;
		if ( isNaN(rating) ){
				rating = 0;
		}

        rating = rating.toFixed(1);

        if(target in this.ratesCurrentWidth == false) {
            this.ratesCurrentWidth[target] = {};
        }

        if(targetId in this.ratesCurrentWidth[target] == false) {
            this.ratesCurrentWidth[target][targetId] = rating * this.ratingPointSize.width;
        }


        ratingContainer.style.background = "url('"+this.ratingSprite+"') bottom left";
        ratingContainer.style.width = this.ratingPointSize.width*5 + 'px';

        var ratingBlock = document.createElement('div');
        ratingBlock.className = this.ratingBlock;
        ratingBlock.title = this.localization + ' ' +rating;
        ratingBlock.style.background = "url('"+this.ratingSprite+"') top left";
        resizeRatingBlock(ratingBlock, this.ratesCurrentWidth[target][targetId], 'left top', this.ratingPointSize.height);

		


		if (this.userCanVote(target, targetId)){ //Checking it just to prevent unnecessary events below

			//On rating block hover change its size and background image
			$(ratingContainer).mousemove(function(event){
                if(_this.userCanVote(target, targetId) === false) {
                    return;
                }
				var hoverRateWidth = Math.ceil((event.clientX-elementOffset(ratingContainer).x)/_this.ratingPointSize.width)*_this.ratingPointSize.width;
				var backgroundPosition = "left center";
                ratingBlock.style.cursor = "pointer";
                resizeRatingBlock(ratingBlock, hoverRateWidth, backgroundPosition);
					
			});


			//On mouse out from rating block return it to previous width
            $(ratingContainer).mouseleave(function(){
                if(_this.userCanVote(target, targetId) == false) {
                    return;
                }
                var backgroundPosition = "left top";
                resizeRatingBlock(ratingBlock, _this.ratesCurrentWidth[target][targetId], backgroundPosition);
			});


			//on rating block click
            $(ratingContainer).click(function(event){
                if(_this.userCanVote(target, targetId) == false) {
                    return;
                }
                var rate = Math.ceil((event.clientX-elementOffset(ratingContainer).x)/_this.ratingPointSize.width);

                _this.vote(target, targetId, rate);  //Trying to vote

				if(_this.userCanVote(target, targetId) == false) {
                    var newRating = ((totalRate + rate) / (1+totalVoters));

                    if (isNaN(newRating)) {
                        newRating = 0;
                    }

                    newRating = newRating.toFixed(1);

                    _this.ratesCurrentWidth[target][targetId] = newRating * _this.ratingPointSize.width;
                    ratingBlock.title = _this.localization + ' ' +newRating;
                    resizeRatingBlock(ratingBlock, _this.ratesCurrentWidth[target][targetId], 'left top');
                }

			});
		}


        ratingContainer.appendChild(ratingBlock);

	};





	//Returns information about items if they are already rated by user either not
	var userVotes = function(targets) {
        $.ajax({
            async: false,
            type: "POST",
            data: {rating_plugin_targets: targets},
            url: "/API/rating_plugin.php?getRating",
            success: function (jsoned) {
                if(jsoned instanceof Object){
                    _this.userVoteInfo = jsoned;
                }
            },
            error:function(){
                console.log('some troubles with rating plugin');
            }
        });
	};




    // Returns bool if user can vote to target
    this.userCanVote = function(target, targetId){
        if(target in this.userVoteInfo === false || targetId in this.userVoteInfo[target] === false){
            return true;
        }

        return !this.userVoteInfo[target][targetId]['userVoted'];

    };







    //Votes for target
	this.vote = function(target, targetId, rate){

	  rate = Number(rate);

	  if(rate>0 && rate<=5){
		$.ajax({
			async: false,
			type: "POST",
			data: {"rating_plugin_target":target, "rating_plugin_target_id" : targetId, "rating_plugin_rating" : rate},
			url: "/API/rating_plugin.php?setRating",
			success: function(result){

                if(result == null){
                    return false;
                }


                if(target in _this.userVoteInfo == false){
                    _this.userVoteInfo[target] = {};
                }
                if(targetId in _this.userVoteInfo[target] == false){
                    _this.userVoteInfo[target][targetId] = {};
                }

                if('totalVoters' in _this.userVoteInfo[target][targetId] == false){
                    _this.userVoteInfo[target][targetId]['totalVoters'] = 0;
                }

                _this.userVoteInfo[target][targetId] = {'target':target, 'userVoted':true, 'totalVoters': _this.userVoteInfo[target][targetId]['totalVoters']++, 'targetId':targetId, 'rating':rate};
                return true;
            },
            error:function(){
                alert('Some difficulties there');
            }
		  });
	   }
	};





    //Sort elements by targets
    var sortTargets = function(targets){

        var targetsSorted = {};

        if(targets instanceof Object){
            for(var i=0; i<targets.length; i++){
                var target = targets[i].getAttribute('data-target');
                var targetId = targets[i].getAttribute('data-target-id');

                if(target in targetsSorted == false){
                    targetsSorted[target] = {};
                }

                targetsSorted[target][targetId] = [targetId];
            }
        }

        return targetsSorted;

    };


    //Changes rating block size and background
    var resizeRatingBlock = function(blockObj, blockWidth, blockBackgroundPosition, blockHeight){

        if(typeof blockHeight === 'undefined'){
            blockHeight = _this.ratingPointSize.height;
        }
        blockObj.style.width = blockWidth + 'px';
        blockObj.style.height = blockHeight + 'px';
        blockObj.style.backgroundPosition = blockBackgroundPosition;
    }




    //Returns elements offset from top and left(some kind analogue to jquery offset())
    var elementOffset = function (element){

        var posX = 0;
        var posY = 0;

        do{
            posX += element.offsetLeft;
            posY += element.offsetTop;
            element = element.offsetParent;
        } while(element != null);

        return {x: posX, y: posY};
    }

}


$(document).ready(function(){
    new Rating().initialize();
});


