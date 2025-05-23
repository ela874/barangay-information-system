<?php

session_start();

// Check if user is logged in and is an admin

if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {

    header("Location: login.php");

    exit();

}

$style = 'main.css';

require("partials/head.php");

require("../database.php");



// Fetch residents from the database

$conn = getDatabaseConnection();

$residents = [];

if ($conn) {

    $query = "SELECT r.id, 
                  CONCAT(r.surname, ', ', r.firstname, ' ', r.middlename) AS fullname, 
                  r.address, r.age, r.sex, r.contact,
                  r.civil_status, r.occupation, r.voter_status,
                  CONCAT(h.id, ': ', hr.surname, ', ', hr.firstname) AS household_name
              FROM residents r
              LEFT JOIN households h ON r.household_id = h.id
              LEFT JOIN residents hr ON h.head_id = hr.id
              ORDER BY r.surname, r.firstname";

    $result = mysqli_query($conn, $query);

    if ($result) {

        while ($row = mysqli_fetch_assoc($result)) {

            $residents[] = $row;

        }

    }

    mysqli_close($conn);

}

?>

<?php require("partials/sidebar.php") ?>

<div class="main-content">

    <div class="header-container">

        <h1>Resident Management</h1>

    </div>

    

    <div class="search-and-add-container">

        <div class="search-box">

            <i class="fas fa-search search-icon"></i>

            <input type="text" id="searchInput" placeholder="Search residents..." onkeyup="searchResidents()">

        </div>

        <button class="text-button add-btn" onclick="addResident()">Add New Resident</button>

    </div>

    

    <div class="table-container">

        <table id="residentTable">

            <thead>

                <tr>

                    <th>No.</th>

                    <th>Full Name</th>

                    <th>Address</th>

                    <th>Age</th>

                    <th>Sex</th>

                    <th>Contact</th>

                    <th>Civil Status</th>

                    <th>Occupation</th>

                    <th>Voter</th>

                    <th>Household</th>

                    <th>Actions</th>

                </tr>

            </thead>

            <tbody id="residentTableBody">

                <?php foreach ($residents as $index => $resident): ?>

                    <tr>

                        <td><?= $index + 1 ?></td>

                        <td><?= htmlspecialchars($resident['fullname']) ?></td>

                        <td><?= htmlspecialchars($resident['address']) ?></td>

                        <td><?= htmlspecialchars($resident['age']) ?></td>

                        <td><?= htmlspecialchars($resident['sex']) ?></td>

                        <td><?= htmlspecialchars($resident['contact']) ?></td>

                        <td><?= htmlspecialchars($resident['civil_status'] ?? 'N/A') ?></td>

                        <td><?= htmlspecialchars($resident['occupation'] ?? 'N/A') ?></td>

                        <td><?= htmlspecialchars($resident['voter_status'] ?? 'N/A') ?></td>

                        <td><?= htmlspecialchars($resident['household_name'] ?? 'N/A') ?></td>

                        <td class="action-buttons">

                            <button class="icon-button view-btn" onclick="openModal('partials/resident-view.php?id=<?= $resident['id'] ?>')" title="View Resident Details">

                                <i class="fas fa-eye"></i>

                            </button>

                            <button class="icon-button edit-btn" onclick="editResident(<?= $resident['id'] ?>)" title="Edit Resident">

                                <i class="fas fa-edit"></i>

                            </button>

                            <button class="icon-button delete-btn" onclick="deleteResident(<?= $resident['id'] ?>)" title="Delete Resident">

                                <i class="fas fa-trash-alt"></i>

                            </button>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>



<!-- Modal Dialog -->
<div id="residentModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <div class="modal-body">
            <!-- Modal content will be loaded here via AJAX -->
        </div>
    </div>
</div>



<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <span class="close-button" id="closeDeleteModal">&times;</span>
        <div class="delete-confirm-content">
            <h3>Confirm Deletion</h3>
            <p>Are you sure you want to delete this resident? This action cannot be undone.</p>
            <div class="button-group">
                <button id="confirmDeleteBtn" class="delete-btn">Yes, Delete</button>
                <button id="cancelDeleteBtn" class="cancel-btn">Cancel</button>
            </div>
        </div>
    </div>
</div>



<script src="../assets/js/residents.js"></script>

<?php require("partials/foot.php"); ?>