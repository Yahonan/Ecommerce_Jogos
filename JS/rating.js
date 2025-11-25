document.addEventListener('DOMContentLoaded', () => {
    const starContainer = document.getElementById('star-rating-container');
    const ratingInput = document.getElementById('avaliacao-estrelas');
    
    if (!starContainer || !ratingInput) return; 

    const stars = starContainer.querySelectorAll('button');

    const filledStar = '<svg class="w-8 h-8 transition-colors fill-current text-yellow-500" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>';
    const emptyStar = '<svg class="w-8 h-8 transition-colors fill-current text-gray-500" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>';

    const updateStars = (rating) => {
        stars.forEach((star, index) => {
            if (index < rating) {
                star.innerHTML = filledStar;
            } else {
                star.innerHTML = emptyStar;
            }
        });
    };
    
    let currentRating = parseInt(ratingInput.value) || 0;
    updateStars(currentRating);


    stars.forEach((star, index) => {
        
        star.addEventListener('click', (e) => {
            e.preventDefault(); 
            currentRating = index + 1;
            ratingInput.value = currentRating;
            updateStars(currentRating); 
        });

        star.addEventListener('mouseenter', () => {
            updateStars(index + 1); 
        });

        star.addEventListener('mouseleave', () => {
            updateStars(currentRating);
        });
    });
});