<style>
    .patient-vitals-card {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #ffffff;
        padding: 18px 20px;
        margin-bottom: 20px;
    }
    .patient-vitals-header {
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
        display: block;
    }
    .patient-vitals-grid {
        display: grid !important;
        grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
        gap: 12px !important;
        width: 100% !important;
    }
    .patient-vitals-grid .vital-item {
        min-width: 0 !important;
        width: 100% !important;
    }
    .patient-vitals-grid .vital-item label {
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .patient-vitals-card .form-control {
        height: 42px !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 8px !important;
        padding: 8px 14px !important;
        font-size: 13.5px !important;
        color: #000000 !important;
    }
    .patient-vitals-card textarea.form-control {
        height: auto !important;
        border-radius: 8px !important;
        resize: vertical;
        color: #000000 !important;
    }
    @media (max-width: 991px) {
        .patient-vitals-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        }
    }
    @media (max-width: 576px) {
        .patient-vitals-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }
    .form-control::placeholder, .form-select::placeholder, textarea.form-control::placeholder {
        color: #94a3b8 !important;
    }
</style>

<div class="col-12">
    <div class="patient-vitals-card">
        <span class="patient-vitals-header">
            VITALS (OPTIONAL)
        </span>
        <div class="patient-vitals-grid">
            <div class="vital-item form-group mb-2">
                <label for="patient_bp" title="B.P (mmHg)"><i class="fas fa-stethoscope me-1 text-danger"></i> B.P (mmHg)</label>
                <input type="text" name="bp" id="patient_bp" class="form-control" placeholder="e.g. 120/80">
            </div>
            <div class="vital-item form-group mb-2">
                <label for="patient_pulse" title="Pulse (bpm)"><i class="fas fa-heartbeat me-1 text-danger"></i> Pulse (bpm)</label>
                <input type="text" name="pulse" id="patient_pulse" class="form-control" placeholder="e.g. 72">
            </div>
            <div class="vital-item form-group mb-2">
                <label for="patient_temperature" title="Temp (°F)"><i class="fas fa-thermometer-half me-1 text-warning"></i> Temp (&deg;F)</label>
                <input type="text" name="temperature" id="patient_temperature" class="form-control" placeholder="e.g. 98.6">
            </div>
            <div class="vital-item form-group mb-2">
                <label for="patient_weight" title="Weight (kg)"><i class="fas fa-weight me-1 text-primary"></i> Weight (kg)</label>
                <input type="text" name="weight" id="patient_weight" class="form-control" placeholder="e.g. 70">
            </div>
            <div class="vital-item form-group mb-2">
                <label for="patient_height" title="Height (cm)"><i class="fas fa-ruler-vertical me-1 text-info"></i> Height (cm)</label>
                <input type="text" name="height" id="patient_height" class="form-control" placeholder="e.g. 175">
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-6 col-12 form-group mb-2">
                <label style="font-size: 13px; font-weight: 600; color: #334155;">
                    <i class="fas fa-history me-1 text-secondary"></i> Patient History
                </label>
                <textarea name="patient_history" id="patient_history" class="form-control" rows="2" placeholder="Enter patient history..."></textarea>
            </div>
            <div class="col-md-6 col-12 form-group mb-2">
                <label style="font-size: 13px; font-weight: 600; color: #334155;">
                    <i class="fas fa-notes-medical me-1 text-success"></i> Patient Complaint
                </label>
                <textarea name="patient_complaint" id="patient_complaint" class="form-control" rows="2" placeholder="Enter patient complaint..."></textarea>
            </div>
            <div class="col-12 form-group mb-0">
                <label style="font-size: 13px; font-weight: 600; color: #334155;">
                    <i class="fas fa-vials me-1" style="color: #6366f1;"></i> Advice for Lab tests
                </label>
                <textarea name="advice_for_lab_tests" id="advice_for_lab_tests" class="form-control" rows="2" placeholder="Enter advice for lab tests..."></textarea>
            </div>
        </div>
    </div>
</div>

