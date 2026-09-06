document.addEventListener('DOMContentLoaded', function() {
    var cookiePopup = document.getElementById('cookiePopup');
    var acceptBtn = document.getElementById('acceptCookies');
    var rejectBtn = document.getElementById('rejectCookies');

    if (!cookiePopup || !acceptBtn || !rejectBtn) {
        console.warn('Cookie popup elements not found');
        return;
    }

    cookiePopup.style.display = 'flex';

    acceptBtn.addEventListener('click', function() {
        cookiePopup.style.display = 'none';
    });

    rejectBtn.addEventListener('click', function() {
        cookiePopup.style.display = 'none';
    });
});

document.addEventListener('DOMContentLoaded', function() {
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap JS is not loaded!');
        return;
    }
    
    const loginModalElement = document.getElementById('loginModal');
    if (loginModalElement && loginModalElement.dataset.autoOpen === 'true') {
        const loginModal = new bootstrap.Modal(loginModalElement);
        loginModal.show();
    }
    
    const signupModalElement = document.getElementById('signupModal');
    if (signupModalElement && signupModalElement.dataset.autoOpen === 'true') {
        const signupModal = new bootstrap.Modal(signupModalElement);
        signupModal.show();
    }
    
    const exampleModalElement = document.getElementById('exampleModal');
    if (exampleModalElement && exampleModalElement.dataset.autoOpen === 'true') {
        const exampleModal = new bootstrap.Modal(exampleModalElement);
        exampleModal.show();
    }
    
    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(function(button) {
        button.addEventListener('click', function() {
            const modal = button.closest('.modal');
            if (modal) {
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) {
                    bsModal.hide();
                } else {
                    modal.classList.remove('show');
                    modal.style.display = 'none';
                    document.body.classList.remove('modal-open');
                    
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                }
            }
        });
    });
    
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const openModals = document.querySelectorAll('.modal.show');
            openModals.forEach(function(modal) {
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) {
                    bsModal.hide();
                }
            });
        }
    });
    
    document.querySelectorAll('.modal').forEach(function(modal) {
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) {
                    bsModal.hide();
                }
            }
        });
    });
});

function toggleRecipe(id) {
    var details = document.getElementById('recipe-details-' + id);
    if (details) {
        if (details.style.display === 'none' || details.style.display === '') {
            details.style.display = 'block';
        } else {
            details.style.display = 'none';
        }
    }
}

function likeRecipe(recipeId) {
    fetch('public/like_recipe.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'recipe_id=' + recipeId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const btn = document.querySelector('[data-recipe-id="' + recipeId + '"]');
            if (btn) {
                const countSpan = btn.querySelector('.count');
                if (countSpan) {
                    countSpan.textContent = data.like_count;
                }
                
                if (data.liked) {
                    btn.classList.add('liked');
                } else {
                    btn.classList.remove('liked');
                }
            }
        } else {
            alert(data.message || 'Failed to like recipe');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const recipeModal = document.getElementById('recipeModal');
    if (recipeModal && recipeModal.dataset.autoOpen === 'true') {
        if (typeof bootstrap !== 'undefined') {
            const modal = new bootstrap.Modal(recipeModal);
            modal.show();
        }
    }
});

function cleanupModalBackdrops() {
    const backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(function(backdrop) {
        backdrop.remove();
    });
    
    document.body.classList.remove('modal-open');
    
    document.body.style.paddingRight = '';
}

window.addEventListener('load', function() {
    setTimeout(cleanupModalBackdrops, 100);
});

document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('hidden.bs.modal', function() {
        setTimeout(cleanupModalBackdrops, 300);
    });
});