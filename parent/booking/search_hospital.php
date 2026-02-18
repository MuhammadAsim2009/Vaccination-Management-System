<?php
// Include authentication and layout files
include '../../config/db.php';
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Fetch approved hospitals
$stmt_hospitals = $conn->prepare("SELECT h.id, h.hospital_name, h.address, h.phone, u.email FROM hospitals h LEFT JOIN users u ON h.user_id = u.id WHERE h.status = 'approved'");
$stmt_hospitals->execute();
$hospital_results = $stmt_hospitals->get_result();

$hospitals = [];

if ($hospital_results->num_rows > 0) {
    while ($row = $hospital_results->fetch_assoc()) {
        $hospitals[] = [
            'id' => $row['id'],
            'name' => $row['hospital_name'],
            'address' => $row['address'],
            'contact' => $row['phone'],
            'email' => $row['email'],
            'type' => 'Hospital',
        ];
    }
}

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
                <div class="col-lg-7 col-md-6">
                    <label class="form-label fw-semibold small text-muted text-uppercase">Search</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-start-0" id="searchInput" placeholder="Hospital name, address or location...">
                    </div>
                </div>

                <!-- Sort Filter -->
                <div class="col-lg-3 col-md-4">
                    <label class="form-label fw-semibold small text-muted text-uppercase">Sort By</label>
                    <select class="form-select" id="sortFilter">
                        <option value="name_asc">Name (A-Z)</option>
                        <option value="name_desc">Name (Z-A)</option>
                        <option value="newest">Newest Added</option>
                    </select>
                </div>

                <!-- Reset Button -->
                <div class="col-lg-2 col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-secondary border w-100 py-2 fw-semibold text-white" id="btnReset">
                        Reset Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3️⃣ Hospital Results Grid -->
    <div class="row g-4 mb-4" id="hospitalGrid">
        <?php foreach($hospitals as $hospital): ?>
        <div class="col-xl-4 col-md-6 hospital-card" 
             data-name="<?= strtolower(htmlspecialchars($hospital['name'])) ?>"
             data-id="<?= $hospital['id'] ?>"
             data-email="<?= strtolower(htmlspecialchars($hospital['email'])) ?>"
             data-phone="<?= strtolower(htmlspecialchars($hospital['contact'])) ?>"
             data-address="<?= strtolower(htmlspecialchars($hospital['address'])) ?>"
             >
            
            <div class="card h-100 border-0 shadow-sm rounded-4 hover-card transition-all">
                <div class="card-body p-4 d-flex flex-column">
                    <!-- Header: Icon & Rating -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-hospital-alt fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0"><?= htmlspecialchars($hospital['name']) ?></h5>
                                <span class="badge bg-light text-muted border mt-2"><?= htmlspecialchars($hospital['type']) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Hospital Info -->
                    <p class="text-muted small mb-2">
                        <i class="fas fa-map-marker-alt me-2 text-secondary" style="width: 16px;"></i><?= htmlspecialchars($hospital['address']) ?>
                    </p>
                    <p class="text-muted small mb-2">
                        <i class="fas fa-envelope me-2 text-secondary" style="width: 16px;"></i><?= htmlspecialchars($hospital['email']) ?>
                    </p>
                    <p class="text-muted small mb-3">
                        <i class="fas fa-phone-alt me-2 text-secondary" style="width: 16px;"></i><?= htmlspecialchars($hospital['contact']) ?>
                    </p>

                    <!-- Action Button -->
                    <div class="mt-auto">
                        <a href="book_vaccination.php?hospital_id=<?= $hospital['id'] ?>" class="btn btn-primary w-100 rounded-pill">
                            Book Appointment <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
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
    const sortFilter = document.getElementById('sortFilter');
    const btnReset = document.getElementById('btnReset');
    const cards = document.querySelectorAll('.hospital-card');
    const grid = document.getElementById('hospitalGrid');
    const noResults = document.getElementById('noResults');

    function filterHospitals() {
        const searchTerm = searchInput.value.toLowerCase();
        let visibleCount = 0;

        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            const email = card.getAttribute('data-email');
            const phone = card.getAttribute('data-phone');
            const address = card.getAttribute('data-address');

            let matchesSearch = name.includes(searchTerm) || address.includes(searchTerm) || email.includes(searchTerm) || phone.includes(searchTerm);

            if (matchesSearch) {
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

    function sortHospitals() {
        const sortValue = sortFilter.value;
        const cardsArray = Array.from(cards);

        cardsArray.sort((a, b) => {
            const nameA = a.getAttribute('data-name');
            const nameB = b.getAttribute('data-name');
            const idA = parseInt(a.getAttribute('data-id'));
            const idB = parseInt(b.getAttribute('data-id'));

            if (sortValue === 'name_asc') {
                return nameA.localeCompare(nameB);
            } else if (sortValue === 'name_desc') {
                return nameB.localeCompare(nameA);
            } else if (sortValue === 'newest') {
                return idB - idA; // Assuming higher ID is newer
            }
            return 0;
        });

        // Re-append sorted cards
        cardsArray.forEach(card => grid.insertBefore(card, noResults));
    }

    // Event Listeners
    searchInput.addEventListener('keyup', filterHospitals);
    sortFilter.addEventListener('change', sortHospitals);

    // Global Reset Function
    window.resetFilters = function() {
        searchInput.value = '';
        sortFilter.value = 'name_asc';
        filterHospitals();
        sortHospitals();
    };

    btnReset.addEventListener('click', window.resetFilters);
});
</script>

<?php include '../includes/footer.php'; ?>
