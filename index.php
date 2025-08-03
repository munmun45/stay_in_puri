<?php include 'includes/header.php'; ?>


<!-- Bootstrap CSS -->

    <style>
        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .offers-section {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            margin: 20px;
            padding: 24px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        .view-all-link {
            color: #1a73e8;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .view-all-link:hover {
            color: #1557b0;
        }

        .nav-tabs {
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 24px;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
            padding: 12px 20px;
            border-bottom: 3px solid transparent;
            background: none;
        }

        .nav-tabs .nav-link.active {
            color: #1a73e8;
            border-bottom-color: #1a73e8;
            background: none;
        }

        .nav-tabs .nav-link:hover {
            color: #1a73e8;
            border-color: transparent;
        }

        .offer-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid #e9ecef;
        }

        .offer-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .card-image {
            height: 180px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .card-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(255, 255, 255, 0.95);
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
        }

        .card-body {
            padding: 20px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .card-subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 16px;
        }

        .card-btn {
            background: #1a73e8;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .card-btn:hover {
            background: #1557b0;
            transform: translateY(-1px);
        }

        .card-btn.btn-outline {
            background: transparent;
            color: #1a73e8;
            border: 2px solid #1a73e8;
        }

        .card-btn.btn-outline:hover {
            background: #1a73e8;
            color: white;
        }

        .bank-logo {
            position: absolute;
            bottom: 12px;
            left: 12px;
            background: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .vacation-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
        }

        .swiper {
            overflow: visible;
        }

        .swiper-slide {
            height: auto;
        }

        .swiper-button-next,
        .swiper-button-prev {
            background: white;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            color: #1a73e8;
            margin-top: -22px;
        }

        .swiper-button-next:after,
        .swiper-button-prev:after {
            font-size: 18px;
            font-weight: bold;
        }

        .swiper-button-disabled {
            opacity: 0.5;
        }

        .swiper-pagination-bullet {
            background: #dee2e6;
            opacity: 1;
        }

        .swiper-pagination-bullet-active {
            background: #1a73e8;
        }

        /* Specific card backgrounds matching the image */
        .card-1 {
            background-image: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
        }

        .card-2 {
            background-image: linear-gradient(135deg, #00cec9 0%, #00b894 100%);
        }

        .card-3 {
            background-image: linear-gradient(135deg, #fdcb6e 0%, #e17055 100%);
        }

        .card-4 {
            background-image: linear-gradient(135deg, #fd79a8 0%, #e84393 100%);
        }

        .card-5 {
            background-image: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
        }

        .vacation-card {
            background-image: linear-gradient(135deg, #2d3436 0%, #636e72 100%);
        }

        @media (max-width: 768px) {
            .offers-section {
                margin: 10px;
                padding: 16px;
            }

            .section-title {
                font-size: 24px;
            }
        }
    </style>


<!-- Simple Image Slider -->

<div class="simple-slider">
    <div class="slider-container">
        <div class="slide active">
            <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80" alt="Puri Beach">
            
        </div>
        <div class="slide">
            <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80" alt="Jagannath Temple">
            
        </div>
        <div class="slide">
            <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80" alt="Odisha Cuisine">
            
        </div>
    </div>
   
</div>


<!-- Search Section -->
<section class="search-section">
    <div class="container">
        <div class="search-tabs">
            <ul class="nav nav-tabs" id="searchTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="hotels-tab" data-bs-toggle="tab" data-bs-target="#hotels" type="button" role="tab" aria-controls="hotels" aria-selected="true">Hotels</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="restaurants-tab" data-bs-toggle="tab" data-bs-target="#restaurants" type="button" role="tab" aria-controls="restaurants" aria-selected="false">Restaurants</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tours-tab" data-bs-toggle="tab" data-bs-target="#tours" type="button" role="tab" aria-controls="tours" aria-selected="false">Tours</button>
                </li>
            </ul>
            <div class="tab-content" id="searchTabContent">
                <div class="tab-pane fade show active" id="hotels" role="tabpanel" aria-labelledby="hotels-tab">
                    <form action="search.php" method="GET" class="search-form">
                        <input type="hidden" name="type" value="hotels">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="destination" class="form-label">Destination</label>
                                <select class="form-select" id="destination" name="destination" required>
                                    <option value="">Select Destination</option>
                                    <option value="puri">Puri</option>
                                    <option value="bhubaneswar">Bhubaneswar</option>
                                    <option value="konark">Konark</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="check-in" class="form-label">Check-in</label>
                                <input type="date" class="form-control" id="check-in" name="check_in" required>
                            </div>
                            <div class="col-md-3">
                                <label for="check-out" class="form-label">Check-out</label>
                                <input type="date" class="form-control" id="check-out" name="check_out" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Similar forms for restaurants and tours -->
            </div>
        </div>
    </div>
</section>












<?php include 'includes/footer.php'; ?>
