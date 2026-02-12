<?php
/**
 * Search Hospital Page
 * Parent Panel
 * 
 * Allows parents to search and filter hospitals based on location and vaccine availability
 * before proceeding to booking.
 * 
 * Path: parent/booking/search_hospital.php
 */

// Include authentication and layout files
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Dummy Data for Hospitals (Simulating Database Fetch)
$hospitals = [
    [
        'id' => 1,
        'name' => 'City General Hospital',
        'address' => 'Main Boulevard, Medical District, Larkana',
        'contact' => '+92 300 1234567',
        'rating' => 4.8,
        'reviews' => 124,
        'vaccines' => ['Polio', 'MMR', 'Hepatitis B', 'BCG'],
        'city' => 'Larkana',
        'type' => 'Public',
        'distance' => '2.5 km'
    ],
    [
        'id' => 2,
        'name' => 'Metro Health Center',
        'address' => 'Sector 4, Near Central Park, Karachi',
        'contact' => '+92 321 9876543',
        'rating' => 4.5,
        'reviews' => 89,
        'vaccines' => ['Influenza', 'DPT', 'Typhoid', 'Polio'],
        'city' => 'Karachi',
        'type' => 'Private',
        'distance' => '5.0 km'
    ],
    [
        'id' => 3,
        'name' => 'Children\'s Wellness Clinic',
        'address' => 'Block 2, Clifton, Karachi',
        'contact' => '+92 333 5551234',
        'rating' => 4.9,
        'reviews' => 210,
        'vaccines' => ['Polio', 'Measles', 'Rotavirus', 'PCV'],
        'city' => 'Karachi',
        'type' => 'Private',
        'distance' => '1.2 km'
    ],
    [
        'id' => 4,
        'name' => 'Community Care Hospital',
        'address' => 'Gulberg III, Lahore',
        'contact' => '+92 300 4445555',
        'rating' => 4.2,
        'reviews' => 56,
        'vaccines' => ['Hepatitis A', 'Hepatitis B', 'Chickenpox'],
        'city' => 'Lahore',
        'type' => 'Public',
        'distance' => '8.4 km'
    ],
    [
        'id' => 5,
        'name' => 'Al-Shifa Medical Center',
        'address' => 'University Road, Peshawar',
        'contact' => '+92 345 6667777',
        'rating' => 4.6,
        'reviews' => 102,
        'vaccines' => ['Polio', 'MMR', 'DPT', 'COVID-19'],
        'city' => 'Peshawar',
        'type' => 'Private',
        'distance' => '3.1 km'
    ],
    [
        'id' => 6,
        'name' => 'National Institute of Child Health',
        'address' => 'Rafiqui Shaheed Road, Karachi',
        'contact' => '+92 21 99201261',
        'rating' => 4.7,
        'reviews' => 340,
        'vaccines' => ['All Basic Vaccines', 'Specialized Care'],
        'city' => 'Karachi',
        'type' => 'Public',
        'distance' => '6.7 km'
    ]
];
?>

<!-- Main Content Container -->
<div class="container-fluid px-4">

    <!-- 1️⃣ Page Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">Search Hospital</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item">Booking</li>
                    <li class="breadcrumb-item active" aria-current="page">Search Hospital</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="../dashboard.php" class="btn btn-outline-secondary shadow-sm rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <!-- 2️⃣ Search & Filter Section -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form id="searchForm" class="row g-3">
                <!-- Search Input -->
                <div class="col-lg-5 col-md-6">
                    <label class="form-label fw-semibold small text-muted text-uppercase">Search</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-start-0" id="searchInput" placeholder="Hospital name, address or location...">
                    </div>
                </div>

                <!-- City Filter -->
                <div class="col-lg-3 col-md-3">
                    <label class="form-label fw-semibold small text-muted text-uppercase">City / Area</label>
                    <select class="form-select" id="cityFilter">
                        <option value="">All Cities</option>
                        <option value="Karachi">Karachi</option>
                        <option value="Lahore">Lahore</option>
                        <option value="Larkana">Larkana</option>
                        <option value="Peshawar">Peshawar</option>
                    </select>
                </div>

                <!-- Vaccine Filter -->
                <div class="col-lg-3 col-md-3">
                    <label class="form-label fw-semibold small text-muted text-uppercase">Available Vaccine</label>
                    <select class="form-select" id="vaccineFilter">
                        <option value="">All Vaccines</option>
                        <option value="Polio">Polio</option>
                        <option value="MMR">MMR</option>
                        <option value="Hepatitis B">Hepatitis B</option>
                        <option value="DPT">DPT</option>
                        <option value="Influenza">Influenza</option>
                    </select>
                </div>

                <!-- Reset Button -->
                <div class="col-lg-1 col-md-12 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-secondary w-100 h-100 py-2" id="btnReset" title="Reset Filters">
                        <i class="fas fa-undo"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3️⃣ Hospital Results Grid -->
    <div class="row g-4 mb-4" id="hospitalGrid">
        <?php foreach($hospitals as $hospital): ?>
        <div class="col-xl-4 col-md-6 hospital-card" 
             data-name="<?= strtolower($hospital['name']) ?>" 
             data-city="<?= $hospital['city'] ?>" 
             data-vaccines="<?= strtolower(implode(',', $hospital['vaccines'])) ?>">
            
            <div class="card h-100 border-0 shadow-sm rounded-4 hover-card transition-all">
                <div class="card-body p-4">
                    <!-- Header: Icon & Rating -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-hospital-alt fs-4"></i>
                            </div>
                            <div>
                                <span class="badge bg-light text-muted border mb-1"><?= $hospital['type'] ?></span>
                                <div class="small text-muted"><i class="fas fa-map-marker-alt me-1"></i> <?= $hospital['distance'] ?> away</div>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-dark d-flex align-items-center justify-content-end gap-1">
                                <span><?= $hospital['rating'] ?></span>
                                <i class="fas fa-star text-warning small"></i>
                            </div>
                            <small class="text-muted" style="font-size: 0.75rem;">(<?= $hospital['reviews'] ?> reviews)</small>
                        </div>
                    </div>

                    <!-- Hospital Info -->
                    <h5 class="fw-bold text-dark mb-2"><?= $hospital['name'] ?></h5>
                    <p class="text-muted small mb-2">
                        <i class="fas fa-location-arrow me-2 text-secondary" style="width: 16px;"></i><?= $hospital['address'] ?>
                    </p>
                    <p class="text-muted small mb-3">
                        <i class="fas fa-phone-alt me-2 text-secondary" style="width: 16px;"></i><?= $hospital['contact'] ?>
                    </p>

                    <hr class="my-3 opacity-10">

                    <!-- Vaccines -->
                    <div class="mb-4">
                        <p class="small fw-semibold text-muted mb-2 text-uppercase">Available Vaccines</p>
                        <div class="d-flex flex-wrap gap-1">
                            <?php foreach($hospital['vaccines'] as $index => $vaccine): ?>
                                <?php if($index < 3): ?>
                                    <span class="badge bg-soft-success text-success border border-success border-opacity-10 fw-normal"><?= $vaccine ?></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php if(count($hospital['vaccines']) > 3): ?>
                                <span class="badge bg-light text-muted border fw-normal">+<?= count($hospital['vaccines']) - 3 ?> more</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <a href="book_vaccination.php?hospital_id=<?= $hospital['id'] ?>" class="btn btn-outline-primary w-100 rounded-pill">
                        Book Appointment <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- No Results Message (Hidden by default) -->
        <div id="noResults" class="col-12 text-center py-5 d-none">
            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                <i class="fas fa-search text-muted fs-2"></i>
            </div>
            <h5 class="fw-bold text-dark">No hospitals found</h5>
            <p class="text-muted">Try adjusting your search or filters to find what you're looking for.</p>
            <button class="btn btn-link text-primary" onclick="resetFilters()">Clear all filters</button>
        </div>
    </div>

    <!-- 4️⃣ Pagination -->
    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item disabled">
                    <a class="page-link border-0 rounded-start-pill" href="#" tabindex="-1">Previous</a>
                </li>
                <li class="page-item active"><a class="page-link border-0 shadow-sm" href="#">1</a></li>
                <li class="page-item"><a class="page-link border-0" href="#">2</a></li>
                <li class="page-item"><a class="page-link border-0" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link border-0 rounded-end-pill" href="#">Next</a>
                </li>
            </ul>
        </nav>
    </div>

</div>

<!-- JavaScript for UI Interactions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const cityFilter = document.getElementById('cityFilter');
    const vaccineFilter = document.getElementById('vaccineFilter');
    const btnReset = document.getElementById('btnReset');
    const cards = document.querySelectorAll('.hospital-card');
    const noResults = document.getElementById('noResults');

    function filterHospitals() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCity = cityFilter.value;
        const selectedVaccine = vaccineFilter.value.toLowerCase();
        let visibleCount = 0;

        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            const city = card.getAttribute('data-city');
            const vaccines = card.getAttribute('data-vaccines');
            const address = card.querySelector('.card-body p').innerText.toLowerCase();

            let matchesSearch = name.includes(searchTerm) || address.includes(searchTerm);
            let matchesCity = selectedCity === '' || city === selectedCity;
            let matchesVaccine = selectedVaccine === '' || vaccines.includes(selectedVaccine);

            if (matchesSearch && matchesCity && matchesVaccine) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Show/Hide No Results Message
        if (visibleCount === 0) {
            noResults.classList.remove('d-none');
        } else {
            noResults.classList.add('d-none');
        }
    }

    // Event Listeners
    searchInput.addEventListener('keyup', filterHospitals);
    cityFilter.addEventListener('change', filterHospitals);
    vaccineFilter.addEventListener('change', filterHospitals);

    // Global Reset Function
    window.resetFilters = function() {
        searchInput.value = '';
        cityFilter.value = '';
        vaccineFilter.value = '';
        filterHospitals();
    };

    btnReset.addEventListener('click', window.resetFilters);
});
</script>

<?php include '../includes/footer.php'; ?>
