<?php include 'includes/header.php'; ?>

<!-- Add Lightgallery CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.0/css/lightgallery.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.0/css/lg-zoom.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.0/css/lg-thumbnail.min.css" />

<!-- Page Header -->
<header class="page-header  py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="display-4 fw-bold">Our Gallery</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Gallery</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</header>

<!-- Gallery Section -->
<section >
    <div class="container">
       
        <div class="text-center mb-4">
            <div class="btn-group filter-button-group" role="group" aria-label="Gallery filter">
                <button type="button" class="btn btn-outline-primary active" data-filter="*">All</button>
                <button type="button" class="btn btn-outline-primary" data-filter=".hotels">Hotels</button>
                <button type="button" class="btn btn-outline-primary" data-filter=".beaches">Beaches</button>
                <button type="button" class="btn btn-outline-primary" data-filter=".temple">Temples</button>
                <button type="button" class="btn btn-outline-primary" data-filter=".food">Local Cuisine</button>
            </div>
        </div>
        
        <!-- Lightgallery Grid -->
        <div class="row g-4" id="lightgallery">
            <!-- Hotel Images -->
            <div class="col-md-4 col-sm-6 gallery-item hotels" data-src="images.jpeg" data-sub-html="<h4>Luxury Suite</h4><p>Experience comfort at its finest</p>">
                <div class="gallery-card">
                    <img src="images.jpeg" alt="Luxury Hotel" class="img-fluid">
                    <div class="gallery-overlay">
                        <div class="gallery-caption">
                            <h5>Luxury Suite</h5>
                            <p>Experience comfort at its finest</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Beach Images -->
            <div class="col-md-4 col-sm-6 gallery-item beaches" data-src="images.jpeg" data-sub-html="<h4>Golden Beach</h4><p>Breathtaking sunsets at Puri Beach</p>">
                <div class="gallery-card">
                    <img src="images.jpeg" alt="Beach Sunset" class="img-fluid">
                    <div class="gallery-overlay">
                        <div class="gallery-caption">
                            <h5>Golden Beach</h5>
                            <p>Breathtaking sunsets</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Temple Images -->
            <div class="col-md-4 col-sm-6 gallery-item temple" data-src="https://source.unsplash.com/1200x800/?temple,india" data-sub-html="<h4>Jagannath Temple</h4><p>Spiritual heritage of Odisha</p>">
                <div class="gallery-card">
                    <img src="https://source.unsplash.com/600x400/?temple,india" alt="Jagannath Temple" class="img-fluid">
                    <div class="gallery-overlay">
                        <div class="gallery-caption">
                            <h5>Jagannath Temple</h5>
                            <p>Spiritual heritage</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Local Cuisine -->
            <div class="col-md-4 col-sm-6 gallery-item food" data-src="https://source.unsplash.com/1200x800/?indian,food" data-sub-html="<h4>Local Delicacies</h4><p>Taste the authentic flavors of Odisha</p>">
                <div class="gallery-card">
                    <img src="https://source.unsplash.com/600x400/?indian,food" alt="Local Cuisine" class="img-fluid">
                    <div class="gallery-overlay">
                        <div class="gallery-caption">
                            <h5>Local Delicacies</h5>
                            <p>Taste of Odisha</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 col-sm-6 gallery-item hotels">
                <div class="gallery-card">
                    <img src="https://source.unsplash.com/600x400/?resort,pool" alt="Resort Pool" class="img-fluid">
                    <div class="gallery-overlay">
                        <div class="gallery-caption">
                            <h5>Infinity Pool</h5>
                            <p>Luxury resort experience</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 col-sm-6 gallery-item beaches">
                <div class="gallery-card">
                    <img src="https://source.unsplash.com/600x400/?beach,water-sports" alt="Water Sports" class="img-fluid">
                    <div class="gallery-overlay">
                        <div class="gallery-caption">
                            <h5>Water Sports</h5>
                            <p>Adventure by the sea</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="bg-light py-5">
    <div class="container text-center">
        <h2 class="mb-4">Ready to Experience Puri?</h2>
        <p class="lead mb-4">Book your stay with us and create your own memories</p>
        <a href="hotels.php" class="btn btn-primary btn-lg">Book Now</a>
    </div>
</section>

<!-- Add Lightgallery JS -->
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/lightgallery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/plugins/zoom/lg-zoom.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/plugins/thumbnail/lg-thumbnail.min.js"></script>

<style>
/* Gallery Styles */
.gallery-card {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
}

.gallery-card img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.gallery-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.gallery-card:hover img {
    transform: scale(1.05);
}

.gallery-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: flex-end;
    opacity: 0;
    transition: opacity 0.3s ease;
    padding: 20px;
}

.gallery-card:hover .gallery-overlay {
    opacity: 1;
}

.gallery-caption {
    color: #fff;
    text-align: left;
    transform: translateY(20px);
    transition: transform 0.3s ease;
}

.gallery-card:hover .gallery-caption {
    transform: translateY(0);
}

.gallery-caption h5 {
    font-weight: 600;
    margin-bottom: 5px;
}

.gallery-caption p {
    margin: 0;
    font-size: 0.9rem;
    opacity: 0.9;
}

/* Filter buttons */
.filter-button-group .btn {
    margin: 2px;
}

.filter-button-group .btn.active {
    background-color: #0d6efd;
    color: white;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lightgallery with proper configuration
    const lightGallery = lightGallery(document.getElementById('lightgallery'), {
        plugins: [lgZoom, lgThumbnail],
        selector: '.gallery-item',
        speed: 500,
        download: false,
        getCaptionFromTitleOrAlt: false,
        mode: 'lg-fade',
        cssEasing: 'cubic-bezier(0.25, 0, 0.25, 1)',
        thumbnail: true,
        showThumbByDefault: false,
        animateThumb: true,
        showThumbnail: true,
        thumbWidth: 80,
        thumbContHeight: 80,
        thumbMargin: 5,
        controls: true,
        mousewheel: true,
        preload: 3,
        dynamic: true,
        dynamicEl: [] // Will be populated dynamically
    });

    // Update lightbox items when filtering
    function updateLightboxItems() {
        const visibleItems = document.querySelectorAll('.gallery-item[style*="display: block"], .gallery-item:not([style])');
        const dynamicEls = [];
        
        visibleItems.forEach(item => {
            dynamicEls.push({
                src: item.getAttribute('data-src'),
                subHtml: item.getAttribute('data-sub-html'),
                thumb: item.querySelector('img').src
            });
        });
        
        // Update lightbox with visible items
        lightGallery.destroy(true);
        lightGallery.init();
    }
    
    // Filter functionality
    const filterButtons = document.querySelectorAll('.filter-button-group button');
    const galleryItems = document.querySelectorAll('.gallery-item');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const filterValue = this.getAttribute('data-filter');
            
            // Filter items
            galleryItems.forEach(item => {
                if (filterValue === '*' || item.classList.contains(filterValue.substring(1))) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Update lightbox items after filtering
            updateLightboxItems();
        });
    });
    
    // Initialize lightbox items on page load
    updateLightboxItems();
});
</script>

<?php include 'includes/footer.php'; ?>
