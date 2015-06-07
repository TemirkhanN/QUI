function Rating(target){
    this.ratingSprite = '/templates/default/images/png/rating/stars.png';
    this.ratingPointSize = {width:15, height:15}; // The rating star(or anything else) width and height
	this.lastRateWidth = 0; // Current rating block width
	this.hoverRateWidth = 0;  // On hover changing rating width
	this.ratingBlock = 'star';   // Ratings block class name
    this.ratingContainer = "js-rating";  // Rating block container class name
    this.localization = 'Рейтинг'; // Localization of "rating".
	var _this = this;  // For namespace vision inside function passed as argument



    this.initialize = function(){

        var ratingViews = document.getElementsByClassName(this.ratingContainer); //Detect all rating blocks on page

        if(ratingViews) {
            for (var i = 0; i < ratingViews.length; i++) {
                this.showRating(ratingViews[i]);
            }
        }

    };
	
	
	// Shows rating for passed element
	this.showRating = function(ratingContainer){

        var target = ratingContainer.getAttribute('data-target');
        var targetId = ratingContainer.getAttribute('data-target-id');
        var totalVoters = ratingContainer.getAttribute('data-rated');
		var totalRate = ratingContainer.getAttribute('data-rating');
		var rating = totalRate / totalVoters;
		if ( isNaN(rating) ){
				rating = 0;
		}
        this.lastRateWidth = rating * this.ratingPointSize.width;

        ratingContainer.style.background = "url('"+this.ratingSprite+"') bottom left";
        ratingContainer.style.width = this.ratingPointSize.width*5 + 'px';

        var ratingBLock = document.createElement('div');
        ratingBLock.className = this.ratingBlock;
        ratingBLock.title = this.localization + ' ' +rating.toFixed(1);
        ratingBLock.style.width = this.lastRateWidth + 'px';
        ratingBLock.style.height = this.ratingPointSize.height + 'px';
        ratingBLock.style.background = "url('"+this.ratingSprite+"') top left";

		


		//Если пользователь не может голосовать
		if (this.userCanVote(target, targetId)){
			//При наведении курсора на рейтинг
			ratingBLock.mousemove(function(event){
				
				if(_this.userVote>0){return;} //Пользователь уже голосовал

				_this.newRateWidth = Math.ceil((event.clientX-$(this).offset().left)/_this.ratingPointSize.width)*_this.ratingPointSize.width;
				$("."+_this.ratingBlock).css({
                    'width': _this.hoverRateWidth+'px',
                    'backgroundPosition': 'left center',
                    'cursor': 'pointer'
                });
					
			});
				
			//При уводе курсора с рейтинга
            ratingBLock.mouseleave(function(){
				
				if(_this.userVote>0){ return;} //Пользователь уже голосовал
				
				$("."+_this.starsDiv).css({'width': _this.lastRateWidth+'px', 'backgroundPosition': 'left top'});
			});
				
			//При клике по рейтингу
            ratingBLock.click(function(event){
				
				if(_this.userCanVote(target, targetId) == false){return;}//Пользователь уже голосовал
				var userVote = _this.Vote(target, targetId, Math.ceil((event.clientX-$(this).offset().left)/(_this.ratingPointSize.width)) );  //Отсылаем запрос к серверу с попыткой проголосовать
				
				if(_this.userCanVote(target, targetId) == false){
					$("."+_this.starsDiv).css({'width': (userVote+totalRate)/(totalVoters+1)*_this.ratingPointSize.width+'px', 'backgroundPosition': 'left top'});
				}

			});


		}


        ratingContainer.appendChild(ratingBLock);

	};

	
	//Возвращает информацию о том голосовал ли пользователь и сколько баллов он поставил
	this.userCanVote = function(target, targetId){
		
		$.ajax({
			async:false,
			type: "GET",
			data: { target : target , target_id : targetId},
			url: "/API/rating/getRating.php",
			success: function(jsoned){
                /*
                voteInfo = JSON.parse(jsoned);

                if(voteInfo.voted===false){
                    _this.userVote = 0;
                } else {
                    _this.userVote = voteInfo.rate;
                }
                */
            }
		});
		return _this.userVote;
		
	};
	

	this.vote = function(target, targetId, rate){

	  rate = Number(rate);

	  
	  if(this.userVote==0 && rate>0 && rate<=5){
		$.ajax({
			async: false,
			type: "POST",
			data: {target:target, target_id : targetId, rating : rate},
			url: "/API/rating/setRating.php",
			success: function(jsoned){
						voteInfo = JSON.parse(jsoned);
					
						if(voteInfo.voted==true){
							_this.userVote = voteInfo.rate;
						} else {
							_this.userVote = 0;
						}
					}
		  });
	   }
	   return this.userVote;
	};

}


$(document).ready(function(){
    new Rating().initialize();
});


