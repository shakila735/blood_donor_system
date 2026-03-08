<?php
require_once __DIR__ . '/../../app/helpers/auth.php';
require_role(['requester']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Blood Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="card shadow border-0">
        <div class="card-header bg-danger text-white">
            <h3 class="mb-0">Post Blood Request</h3>
        </div>
        <div class="card-body">
            <div id="messageBox"></div>

            <form id="bloodRequestForm">
                <div class="mb-3">
                    <label for="patient_name" class="form-label">Patient Name</label>
                    <input type="text" class="form-control" id="patient_name" required>
                </div>

                <div class="mb-3">
                    <label for="blood_group" class="form-label">Blood Group</label>
                    <select class="form-select" id="blood_group" required>
                        <option value="">Select Blood Group</option>
                        <option>A+</option>
                        <option>A-</option>
                        <option>B+</option>
                        <option>B-</option>
                        <option>AB+</option>
                        <option>AB-</option>
                        <option>O+</option>
                        <option>O-</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="units_needed" class="form-label">Units Needed</label>
                    <input type="number" class="form-control" id="units_needed" min="1" required>
                </div>

                <div class="mb-3">
                    <label for="needed_date" class="form-label">Needed Date</label>
                    <input type="date" class="form-control" id="needed_date" required>
                </div>

                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="location" required>
                </div>

                <div class="mb-3">
                    <label for="hospital_name" class="form-label">Hospital Name</label>
                    <input type="text" class="form-control" id="hospital_name">
                </div>

                <div class="mb-3">
                    <label for="contact_phone" class="form-label">Contact Phone</label>
                    <input type="text" class="form-control" id="contact_phone" required>
                </div>

                <div class="mb-3">
                    <label for="details" class="form-label">Details</label>
                    <textarea class="form-control" id="details" rows="4" placeholder="Write extra details if needed"></textarea>
                </div>

                <button type="submit" class="btn btn-danger">Submit Request</button>
                <a href="dashboard.php" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById("bloodRequestForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    const payload = {
        patient_name: document.getElementById("patient_name").value.trim(),
        blood_group: document.getElementById("blood_group").value,
        units_needed: document.getElementById("units_needed").value,
        needed_date: document.getElementById("needed_date").value,
        location: document.getElementById("location").value.trim(),
        hospital_name: document.getElementById("hospital_name").value.trim(),
        contact_phone: document.getElementById("contact_phone").value.trim(),
        details: document.getElementById("details").value.trim()
    };

    const response = await fetch("../api/blood_request_create.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(payload)
    });

    const result = await response.json();
    const box = document.getElementById("messageBox");

    if (result.success) {
        box.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
        document.getElementById("bloodRequestForm").reset();
    } else {
        let errorHtml = "";
        if (Array.isArray(result.data) && result.data.length > 0) {
            errorHtml = "<ul>";
            result.data.forEach(item => {
                errorHtml += `<li>${item}</li>`;
            });
            errorHtml += "</ul>";
        }

        box.innerHTML = `<div class="alert alert-danger">${result.message}${errorHtml}</div>`;
    }
});
</script>

</body>
</html>