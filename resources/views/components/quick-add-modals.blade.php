<!-- Quick Add Modals (Patient, Treatment, Doctor) -->
<style>
    /* Theme button styling for Quick Add Modals */
    .quick-modal-save-btn {
        background-color: #cfece0 !important;
        border-color: #cfece0 !important;
        color: #000000 !important;
        font-weight: 600 !important;
        border-radius: 50px !important;
        padding: 8px 24px !important;
        transition: all 0.2s ease-in-out !important;
    }
    .quick-modal-save-btn:hover,
    .quick-modal-save-btn:focus,
    .quick-modal-save-btn:active {
        background-color: #b5e2cf !important;
        border-color: #b5e2cf !important;
        color: #000000 !important;
        box-shadow: none !important;
    }
    .quick-modal-cancel-btn {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: #ffffff !important;
        font-weight: 500 !important;
        border-radius: 50px !important;
        padding: 8px 20px !important;
    }
    .quick-modal-cancel-btn:hover,
    .quick-modal-cancel-btn:focus {
        background-color: #5a6268 !important;
        border-color: #545b62 !important;
        color: #ffffff !important;
    }
    .quick-modal-add-sm-btn {
        background-color: #cfece0 !important;
        border-color: #cfece0 !important;
        color: #000000 !important;
        font-weight: 600 !important;
        border-radius: 50px !important;
        padding: 4px 14px !important;
        font-size: 12px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        box-shadow: none !important;
    }
    .quick-modal-add-sm-btn:hover {
        background-color: #b5e2cf !important;
        border-color: #b5e2cf !important;
        color: #000000 !important;
    }
    body:not(.modal-open) .modal-backdrop {
        display: none !important;
    }
</style>

<!-- Quick Add Patient Modal -->
<div class="modal fade" id="quickAddPatientModal" tabindex="-1" role="dialog" aria-labelledby="quickAddPatientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header" style="background-color: #CFECE0; color: #000;">
                <h5 class="modal-title font-weight-bold" id="quickAddPatientModalLabel">
                    <i class="fas fa-user-plus mr-2"></i> Add Patient
                </h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background: transparent; border: none; font-size: 1.5rem; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickAddPatientForm" novalidate>
                <div class="modal-body p-4">
                    <div id="quickPatientAlert" class="alert alert-danger" style="display: none;"></div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold"><i class="fas fa-user icon-style"></i> Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="fullname" placeholder="Enter Full Name" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold"><i class="fas fa-phone icon-style"></i> Phone <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="phone" placeholder="Enter 10 Digit Phone" maxlength="10" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold"><i class="fas fa-venus-mars icon-style"></i> Gender</label>
                                <select class="form-control" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold"><i class="fas fa-calendar-alt icon-style"></i> Age</label>
                                <input type="number" class="form-control" name="age" placeholder="Age" min="0" max="150">
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="font-weight-bold"><i class="fas fa-envelope icon-style"></i> Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Enter Email (Optional)">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn quick-modal-cancel-btn" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn quick-modal-save-btn" id="btnSaveQuickPatient">
                        <span class="spinner-border spinner-border-sm me-1 text-dark" style="display:none;"></span> Save Patient
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Add Treatment Modal -->
<div class="modal fade" id="quickAddTreatmentModal" tabindex="-1" role="dialog" aria-labelledby="quickAddTreatmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header" style="background-color: #CFECE0; color: #000;">
                <h5 class="modal-title font-weight-bold" id="quickAddTreatmentModalLabel">
                    <i class="fas fa-pills mr-2"></i> Add Treatment
                </h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background: transparent; border: none; font-size: 1.5rem; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickAddTreatmentForm" novalidate>
                <div class="modal-body p-4">
                    <div id="quickTreatmentAlert" class="alert alert-danger" style="display: none;"></div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold"><i class="fas fa-cogs icon-style"></i> Treatment Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="Enter Treatment Name" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold"><i class="fas fa-money-bill-wave icon-style"></i> Price <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="price" placeholder="Enter Price" min="0" step="0.01" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold mb-1"><i class="fas fa-user-md icon-style"></i> Doctor <span class="text-danger">*</span></label>
                        <select class="form-control" name="doctor_id" id="quickTreatmentDoctorSelect" required>
                            <option value="">Select Doctor</option>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="font-weight-bold"><i class="fas fa-clipboard-list icon-style"></i> Description</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="Brief description (Optional)" style="border-radius: 10px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn quick-modal-cancel-btn" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn quick-modal-save-btn" id="btnSaveQuickTreatment">
                        <span class="spinner-border spinner-border-sm me-1 text-dark" style="display:none;"></span> Save Treatment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Add Doctor Modal -->
<div class="modal fade" id="quickAddDoctorModal" tabindex="-1" role="dialog" aria-labelledby="quickAddDoctorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header" style="background-color: #CFECE0; color: #000;">
                <h5 class="modal-title font-weight-bold" id="quickAddDoctorModalLabel">
                    <i class="fas fa-user-md mr-2"></i> Add Doctor
                </h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background: transparent; border: none; font-size: 1.5rem; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickAddDoctorForm" novalidate>
                <div class="modal-body p-4">
                    <div id="quickDoctorAlert" class="alert alert-danger" style="display: none;"></div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold"><i class="fas fa-user icon-style"></i> Doctor Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="fullname" placeholder="Enter Full Name" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold"><i class="fas fa-envelope icon-style"></i> Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" placeholder="Enter Email" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold"><i class="fas fa-phone icon-style"></i> Phone <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="phone" placeholder="Enter 10 Digit Phone" maxlength="10" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold"><i class="fas fa-lock icon-style"></i> Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="password" placeholder="Min 6 characters" minlength="6" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold"><i class="fas fa-venus-mars icon-style"></i> Gender</label>
                                <select class="form-control" name="gender">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn quick-modal-cancel-btn" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn quick-modal-save-btn" id="btnSaveQuickDoctor">
                        <span class="spinner-border spinner-border-sm me-1 text-dark" style="display:none;"></span> Save Doctor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Add Machine Modal -->
<div class="modal fade" id="quickAddMachineModal" tabindex="-1" role="dialog" aria-labelledby="quickAddMachineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header" style="background-color: #CFECE0; color: #000;">
                <h5 class="modal-title font-weight-bold" id="quickAddMachineModalLabel">
                    <i class="fas fa-cogs mr-2"></i> Add Machine
                </h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background: transparent; border: none; font-size: 1.5rem; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickAddMachineForm" novalidate>
                <div class="modal-body p-4">
                    <div id="quickMachineAlert" class="alert alert-danger" style="display: none;"></div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold"><i class="fas fa-cogs icon-style"></i> Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="Enter Machine Title" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold"><i class="fas fa-money-bill-wave icon-style"></i> Price <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="price" placeholder="Enter Price" min="0" step="0.01" required>
                    </div>
                    <div class="form-group mb-2">
                        <label class="font-weight-bold"><i class="fas fa-clipboard-list icon-style"></i> Description</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Brief description (Optional)" style="border-radius: 10px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn quick-modal-cancel-btn" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn quick-modal-save-btn" id="btnSaveQuickMachine">
                        <span class="spinner-border spinner-border-sm me-1 text-dark" style="display:none;"></span> Save Machine
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Add Diagnosis Modal -->
<div class="modal fade" id="quickAddDiagnosisModal" tabindex="-1" role="dialog" aria-labelledby="quickAddDiagnosisModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header" style="background-color: #CFECE0; color: #000;">
                <h5 class="modal-title font-weight-bold" id="quickAddDiagnosisModalLabel">
                    <i class="fas fa-medkit mr-2"></i> Add Diagnosis
                </h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background: transparent; border: none; font-size: 1.5rem; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickAddDiagnosisForm" novalidate>
                <div class="modal-body p-4">
                    <div id="quickDiagnosisAlert" class="alert alert-danger" style="display: none;"></div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold"><i class="fas fa-medkit icon-style"></i> Diagnosis Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="Enter Diagnosis Name" required>
                    </div>
                    <div class="form-group mb-2">
                        <label class="font-weight-bold"><i class="fas fa-clipboard-list icon-style"></i> Description</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Brief description (Optional)" style="border-radius: 10px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn quick-modal-cancel-btn" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn quick-modal-save-btn" id="btnSaveQuickDiagnosis">
                        <span class="spinner-border spinner-border-sm me-1 text-dark" style="display:none;"></span> Save Diagnosis
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Add Symptom Modal -->
<div class="modal fade" id="quickAddSymptomModal" tabindex="-1" role="dialog" aria-labelledby="quickAddSymptomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header" style="background-color: #CFECE0; color: #000;">
                <h5 class="modal-title font-weight-bold" id="quickAddSymptomModalLabel">
                    <i class="fas fa-notes-medical mr-2"></i> Add Symptom
                </h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background: transparent; border: none; font-size: 1.5rem; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickAddSymptomForm" novalidate>
                <div class="modal-body p-4">
                    <div id="quickSymptomAlert" class="alert alert-danger" style="display: none;"></div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold"><i class="fas fa-notes-medical icon-style"></i> Symptom Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="Enter Symptom Name" required>
                    </div>
                    <div class="form-group mb-2">
                        <label class="font-weight-bold"><i class="fas fa-clipboard-list icon-style"></i> Details</label>
                        <textarea class="form-control" name="details" rows="3" placeholder="Details (Optional)" style="border-radius: 10px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn quick-modal-cancel-btn" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn quick-modal-save-btn" id="btnSaveQuickSymptom">
                        <span class="spinner-border spinner-border-sm me-1 text-dark" style="display:none;"></span> Save Symptom
                    </button>
                </div>
            </form>
<!-- Quick Add Therapy Modal -->
<div class="modal fade" id="quickAddTherapyModal" tabindex="-1" role="dialog" aria-labelledby="quickAddTherapyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header" style="background-color: #CFECE0; color: #000;">
                <h5 class="modal-title font-weight-bold" id="quickAddTherapyModalLabel">
                    <i class="fas fa-spa mr-2"></i> Add Therapy
                </h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background: transparent; border: none; font-size: 1.5rem; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickAddTherapyForm" novalidate>
                <div class="modal-body p-4">
                    <div id="quickTherapyAlert" class="alert alert-danger" style="display: none;"></div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold"><i class="fas fa-spa icon-style"></i> Therapy Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="Enter Therapy Name" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold"><i class="fas fa-clock icon-style"></i> Duration (Mins) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="duration_minutes" placeholder="e.g. 30" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold"><i class="fas fa-money-bill-wave icon-style"></i> Cost <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="cost" placeholder="Cost" min="0" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="font-weight-bold"><i class="fas fa-clipboard-list icon-style"></i> Description</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Brief description (Optional)" style="border-radius: 10px;"></textarea>
                    </div>
                    <input type="hidden" name="gst_option" value="Without GST">
                    <input type="hidden" name="status" value="active">
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn quick-modal-cancel-btn" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn quick-modal-save-btn" id="btnSaveQuickTherapy">
                        <span class="spinner-border spinner-border-sm me-1 text-dark" style="display:none;"></span> Save Therapy
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Add Vendor Modal -->
<div class="modal fade" id="quickAddSupplierModal" tabindex="-1" role="dialog" aria-labelledby="quickAddSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header" style="background-color: #CFECE0; color: #000;">
                <h5 class="modal-title font-weight-bold" id="quickAddSupplierModalLabel">
                    <i class="fas fa-handshake mr-2"></i> Add Vendor
                </h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background: transparent; border: none; font-size: 1.5rem; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickAddSupplierForm" novalidate>
                <div class="modal-body p-4">
                    <div id="quickSupplierAlert" class="alert alert-danger" style="display: none;"></div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold"><i class="fa fa-hospital-o icon-style"></i> Company Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="Enter Company Name" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold"><i class="fas fa-user icon-style"></i> Contact Person <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="contact_person" placeholder="Enter Contact Person" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold"><i class="fas fa-phone icon-style"></i> Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="phone" placeholder="Enter 10 Digit Phone" maxlength="10" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold"><i class="fas fa-envelope icon-style"></i> Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" placeholder="Enter Email" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="font-weight-bold"><i class="fas fa-map-marker-alt icon-style"></i> Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="address" rows="2" placeholder="Enter Address" style="border-radius: 10px;" required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn quick-modal-cancel-btn" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn quick-modal-save-btn" id="btnSaveQuickSupplier">
                        <span class="spinner-border spinner-border-sm me-1 text-dark" style="display:none;"></span> Save Vendor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Global default callbacks to immediately append and select newly created records in the DOM
    window._quickAddCallbacks = {
        patient: function(id, res) {
            let name = res?.patient?.fullname || res?.patient?.name || res?.data?.fullname || res?.data?.name || 'New Patient';
            name = name.charAt(0).toUpperCase() + name.slice(1);
            let treatmentId = res?.patient?.treatment_id || res?.data?.treatment_id || '';
            let selectors = 'select[name="patient_id"], #patientDropdown, #patient_id, .patientDropdown, #patientDropdownModal, select[name="patientId"]';
            $(selectors).each(function() {
                let $sel = $(this);
                if ($sel.find(`option[value="${id}"]`).length === 0) {
                    let opt = new Option(name, id, true, true);
                    opt.selected = true;
                    if (treatmentId) $(opt).attr('data-treatment-id', treatmentId);
                    $sel.append(opt);
                }
                $sel.val(String(id)).trigger('change');
                try {
                    $sel.trigger({
                        type: 'select2:select',
                        params: { data: { id: id, text: name } }
                    });
                } catch(e) {}
            });
        },
        treatment: function(id, res) {
            let name = res?.treatment?.name || res?.data?.name || res?.name || 'New Treatment';
            name = name.charAt(0).toUpperCase() + name.slice(1);
            let doctorId = res?.treatment?.doctor_id || res?.data?.doctor_id || '';
            let price = res?.treatment?.price !== undefined ? res.treatment.price : (res?.data?.price !== undefined ? res.data.price : (res?.price || ''));
            let selectors = 'select[name="treatment_id"], #treatmentDropdown, #treatmentDropdownforbooking, .type-service-select, select[name="treatmentId"]';
            $(selectors).each(function() {
                let $sel = $(this);
                if ($sel.find(`option[value="${id}"]`).length === 0) {
                    let opt = new Option(name, id, true, true);
                    opt.selected = true;
                    if (doctorId) $(opt).attr('data-doctor-id', doctorId);
                    if (price !== '') $(opt).attr('data-price', price);
                    $sel.append(opt);
                }
                $sel.val(String(id)).trigger('change');
                try {
                    $sel.trigger({
                        type: 'select2:select',
                        params: { data: { id: id, text: name, price: price, doctor_id: doctorId } }
                    });
                } catch(e) {}
            });
        },
        doctor: function(id, res) {
            let name = res?.user?.fullname || res?.user?.name || res?.data?.fullname || res?.data?.name || 'Doctor';
            name = name.charAt(0).toUpperCase() + name.slice(1);
            let selectors = 'select[name="doctor_id"], select[name="user_id"], select[name="staff_id"], #doctorSelect, #userdropdown, #doctorDropdown, .doctorSelect, #userSelect';
            $(selectors).each(function() {
                let $sel = $(this);
                if ($sel.find(`option[value="${id}"]`).length === 0) {
                    let opt = new Option(name, id, true, true);
                    opt.selected = true;
                    $sel.append(opt);
                }
                $sel.val(String(id)).trigger('change');
                try {
                    $sel.trigger({
                        type: 'select2:select',
                        params: { data: { id: id, text: name } }
                    });
                } catch(e) {}
            });
        },
        machine: function(id, res) {
            let name = res?.machine?.name || res?.data?.name || res?.name || 'Machine';
            name = name.charAt(0).toUpperCase() + name.slice(1);
            let price = res?.machine?.price || res?.data?.price || res?.price || 0;
            let selectors = 'select[name="machine_id[]"], select[name="machine_id"], #machineDropdown';
            $(selectors).each(function() {
                let $sel = $(this);
                if ($sel.find(`option[value="${id}"]`).length === 0) {
                    let opt = new Option(name, id, true, true);
                    $(opt).attr('data-price', price);
                    $sel.append(opt);
                }
                let currentVals = $sel.val() || [];
                if (Array.isArray(currentVals)) {
                    if (!currentVals.includes(String(id))) currentVals.push(String(id));
                    $sel.val(currentVals);
                } else {
                    $sel.val(String(id));
                }
                $sel.trigger('change');
            });
        },
        diagnosis: function(id, res) {
            let name = res?.diagnosis?.name || res?.data?.name || res?.name || 'Diagnosis';
            name = name.charAt(0).toUpperCase() + name.slice(1);
            let selectors = 'select[name="diagnosis_id[]"], select[name="diagnosis_id"], #diagnosisDropdown';
            $(selectors).each(function() {
                let $sel = $(this);
                if ($sel.find(`option[value="${id}"]`).length === 0) {
                    let opt = new Option(name, id, true, true);
                    $sel.append(opt);
                }
                let currentVals = $sel.val() || [];
                if (Array.isArray(currentVals)) {
                    if (!currentVals.includes(String(id))) currentVals.push(String(id));
                    $sel.val(currentVals);
                } else {
                    $sel.val(String(id));
                }
                $sel.trigger('change');
            });
        },
        symptom: function(id, res) {
            let name = res?.symptom?.name || res?.data?.name || res?.name || 'Symptom';
            name = name.charAt(0).toUpperCase() + name.slice(1);
            let selectors = 'select[name="symptoms[]"], select[name="symptoms"], #symptomDropdown';
            $(selectors).each(function() {
                let $sel = $(this);
                if ($sel.find(`option[value="${id}"]`).length === 0) {
                    let opt = new Option(name, id, true, true);
                    $sel.append(opt);
                }
                let currentVals = $sel.val() || [];
                if (Array.isArray(currentVals)) {
                    if (!currentVals.includes(String(id))) currentVals.push(String(id));
                    $sel.val(currentVals);
                } else {
                    $sel.val(String(id));
                }
                $sel.trigger('change');
            });
        },
        therapy: function(id, res) {
            let name = res?.data?.name || res?.therapy?.name || 'Therapy';
            name = name.charAt(0).toUpperCase() + name.slice(1);
            let selectors = 'select[name="therapy_id"], #therapyDropdown';
            $(selectors).each(function() {
                let $sel = $(this);
                if ($sel.find(`option[value="${id}"]`).length === 0) {
                    let opt = new Option(name, id, true, true);
                    opt.selected = true;
                    $sel.append(opt);
                }
                $sel.val(String(id)).trigger('change');
                try {
                    $sel.trigger({
                        type: 'select2:select',
                        params: { data: { id: id, text: name } }
                    });
                } catch(e) {}
            });
        },
        supplier: function(id, res) {
            let name = res?.supplier?.name || res?.data?.name || 'Supplier';
            name = name.charAt(0).toUpperCase() + name.slice(1);
            let selectors = 'select[name="supplier_id"], #supplierDropdown, #supplier_id';
            $(selectors).each(function() {
                let $sel = $(this);
                if ($sel.find(`option[value="${id}"]`).length === 0) {
                    let opt = new Option(name, id, true, true);
                    opt.selected = true;
                    $sel.append(opt);
                }
                $sel.val(String(id)).trigger('change');
                try {
                    $sel.trigger({
                        type: 'select2:select',
                        params: { data: { id: id, text: name } }
                    });
                } catch(e) {}
            });
        }
    };

    // Chain the generic DOM update helper with the caller-provided callback
    window._chainCallback = function(type, id, res, userCb) {
        if (window._quickAddCallbacks && typeof window._quickAddCallbacks[type] === 'function') {
            window._quickAddCallbacks[type](id, res);
        }
        if (typeof userCb === 'function') {
            userCb(id, res);
        }
    };

    // Shared JavaScript helper to initialize and bind Quick Add Modals
    window.initQuickAddModals = function(options) {
        options = options || {};
        const getToken = options.getToken || function() {
            return @json(session('access_token')) || localStorage.getItem('authToken');
        };
        const getBranchId = options.getBranchId || function() {
            let b = localStorage.getItem('selectedBranchId');
            return (b && b !== 'null' && b !== 'undefined' && b !== '') ? b : '1';
        };

        const onPatientAdded = options.onPatientAdded || function(id) {};
        const onTreatmentAdded = options.onTreatmentAdded || function(id) {};
        const onDoctorAdded = options.onDoctorAdded || function(id) {};
        const onMachineAdded = options.onMachineAdded || function(id) {};
        const onDiagnosisAdded = options.onDiagnosisAdded || function(id) {};
        const onSymptomAdded = options.onSymptomAdded || function(id) {};
        const onTherapyAdded = options.onTherapyAdded || function(id) {};
        const onSupplierAdded = options.onSupplierAdded || function(id) {};

        // Helper to extract clear error message from xhr or response
        function parseAjaxError(xhr, defaultMsg) {
            let errorMsg = defaultMsg || 'An error occurred.';
            let res = xhr ? (xhr.responseJSON || xhr) : null;
            if (typeof res === 'string') {
                try { res = JSON.parse(res); } catch(e) {}
            }
            if (res) {
                if (res.errors && typeof res.errors === 'object') {
                    let errs = [];
                    $.each(res.errors, function(k, v) {
                        if (Array.isArray(v)) {
                            errs.push(v.join('<br>'));
                        } else if (typeof v === 'string') {
                            errs.push(v);
                        }
                    });
                    if (errs.length) return errs.join('<br>');
                }
                if (res.message) return res.message;
                if (res.error) return res.error;
            }
            if (xhr && xhr.responseText) {
                try {
                    let parsed = JSON.parse(xhr.responseText);
                    if (parsed.message) return parsed.message;
                    if (parsed.error) return parsed.error;
                } catch(e) {}
            }
            return errorMsg;
        }

        // Helper to hide modal supporting both bootstrap 4 and 5
        function closeModal(selector) {
            $(selector).modal('hide');
            try {
                let el = document.querySelector(selector);
                if (el && window.bootstrap && bootstrap.Modal) {
                    let instance = bootstrap.Modal.getInstance(el);
                    if (instance) instance.hide();
                }
            } catch(e) {}

            setTimeout(function() {
                $('body').removeClass('modal-open').css({ 'overflow': '', 'padding-right': '' });
                $('.modal-backdrop').remove();
            }, 350);
        }

        // Helper to show modal supporting both bootstrap 4 and 5
        function showModal(selector) {
            $(selector).modal('show');
            try {
                let el = document.querySelector(selector);
                if (el && window.bootstrap && bootstrap.Modal) {
                    let instance = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
                    if (instance) instance.show();
                }
            } catch(e) {}
        }



        // Enforce digits-only on all phone fields across quick-add modals
        $(document).on('input', '#quickAddPatientModal input[name="phone"], #quickAddDoctorModal input[name="phone"]', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        $(document).on('keypress', '#quickAddPatientModal input[name="phone"], #quickAddDoctorModal input[name="phone"]', function(e) {
            var charCode = (e.which) ? e.which : e.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                e.preventDefault();
                return false;
            }
            return true;
        });

        // Clear validation errors and ensure backdrops are cleared when modals close
        $('.modal').on('hidden.bs.modal', function() {
            let $form = $(this).find('form');
            if ($form.length && $form.data('validator')) {
                $form.data('validator').resetForm();
            }
            $form.find('.is-invalid').removeClass('is-invalid');
            $(this).find('.alert-danger').hide().empty();

            if ($('.modal.show').length === 0) {
                $('body').removeClass('modal-open').css({ 'overflow': '', 'padding-right': '' });
                $('.modal-backdrop').remove();
            }
        });

        // Auto-load doctors into quickTreatmentDoctorSelect
        function loadQuickTreatmentDoctors(selectedId) {
            let token = getToken();
            let branchId = getBranchId();
            let $select = $('#quickTreatmentDoctorSelect');
            if (!$select.length) return;

            let prevVal = selectedId !== undefined ? selectedId : $select.val();

            $.ajax({
                url: "{{ url('/api/doctors') }}",
                type: "GET",
                data: { branch_id: branchId },
                dataType: "json",
                headers: token ? { "Authorization": "Bearer " + token } : {},
                xhrFields: { withCredentials: true },
                success: function(response) {
                    let doctors = response.doctors || response.data || [];
                    $select.empty().append('<option value="">Select Doctor</option>');
                    $.each(doctors, function(index, doctor) {
                        let name = (doctor.fullname || doctor.name || '');
                        if (name) {
                            name = name.charAt(0).toUpperCase() + name.slice(1);
                        }
                        $select.append(`<option value="${doctor.id}">${name}</option>`);
                    });
                    if (prevVal) {
                        $select.val(prevVal);
                    }
                },
                error: function(xhr) {
                    console.warn("Could not load doctors for quick treatment modal:", xhr.status, xhr.responseText);
                }
            });
        }

        // Fetch doctors whenever Add Treatment modal is opened
        $('#quickAddTreatmentModal').on('show.bs.modal', function() {
            loadQuickTreatmentDoctors();
        });

        // Initial load of doctors if select is present
        loadQuickTreatmentDoctors();

        // Setup jQuery Validation for a form
        function setupValidation(formSelector, rules, messages, submitHandler) {
            let $form = $(formSelector);
            if ($.fn.validate) {
                $form.validate({
                    rules: rules,
                    messages: messages,
                    errorElement: 'span',
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-group').append(error);
                    },
                    highlight: function(element, errorClass, validClass) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                    },
                    submitHandler: function(form, event) {
                        if (event) event.preventDefault();
                        submitHandler.call(form);
                        return false;
                    }
                });
            } else {
                $form.off('submit').on('submit', function(e) {
                    e.preventDefault();
                    submitHandler.call(this);
                });
            }
        }

        // 1. Quick Add Patient Submit & Validation
        setupValidation(
            '#quickAddPatientForm',
            {
                fullname: {
                    required: true,
                    minlength: 2
                },
                phone: {
                    required: true,
                    digits: true,
                    minlength: 10,
                    maxlength: 10
                },
                email: {
                    email: true
                },
                age: {
                    number: true,
                    min: 0,
                    max: 150
                }
            },
            {
                fullname: {
                    required: "Please enter patient full name",
                    minlength: "Name must be at least 2 characters"
                },
                phone: {
                    required: "Please enter phone number",
                    digits: "Please enter only digits",
                    minlength: "Phone number must be exactly 10 digits",
                    maxlength: "Phone number must be exactly 10 digits"
                },
                email: {
                    email: "Please enter a valid email address"
                },
                age: {
                    number: "Please enter a valid age",
                    min: "Age cannot be negative",
                    max: "Please enter a valid age"
                }
            },
            function() {
                let form = this;
                let $btn = $('#btnSaveQuickPatient');
                let $alert = $('#quickPatientAlert');
                $alert.hide().empty();

                let token = getToken();
                let branchId = getBranchId();

                let formData = new FormData(form);
                formData.append('branch_id', branchId);

                $btn.prop('disabled', true).find('.spinner-border').show();

                $.ajax({
                    url: "{{ url('api/patient') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { "Authorization": "Bearer " + token },
                    success: function(response) {
                        $btn.prop('disabled', false).find('.spinner-border').hide();
                        if (response.status === false) {
                            $alert.html(response.message || 'Error creating patient.').show();
                            return;
                        }
                        form.reset();
                        $(form).find('.is-invalid').removeClass('is-invalid');
                        closeModal('#quickAddPatientModal');

                        let newPatientId = response.patient?.id || response.data?.id;
                        window._chainCallback('patient', newPatientId, response, onPatientAdded);
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).find('.spinner-border').hide();
                        let errorMsg = parseAjaxError(xhr, 'Failed to create patient.');
                        $alert.html(errorMsg).show();
                    }
                });
            }
        );

        // 2. Quick Add Treatment Submit & Validation
        setupValidation(
            '#quickAddTreatmentForm',
            {
                name: {
                    required: true,
                    minlength: 2
                },
                price: {
                    required: true,
                    number: true,
                    min: 0
                },
                doctor_id: {
                    required: true
                }
            },
            {
                name: {
                    required: "Please enter treatment name",
                    minlength: "Treatment name must be at least 2 characters"
                },
                price: {
                    required: "Please enter price",
                    number: "Please enter a valid number",
                    min: "Price cannot be negative"
                },
                doctor_id: {
                    required: "Please select a doctor"
                }
            },
            function() {
                let form = this;
                let $btn = $('#btnSaveQuickTreatment');
                let $alert = $('#quickTreatmentAlert');
                $alert.hide().empty();

                let token = getToken();
                let branchId = getBranchId();

                let formData = new FormData(form);
                formData.append('branch_id', branchId);
                formData.append('gst_option', 'Without GST');

                $btn.prop('disabled', true).find('.spinner-border').show();

                $.ajax({
                    url: "{{ url('api/treatments') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { "Authorization": "Bearer " + token },
                    success: function(response) {
                        $btn.prop('disabled', false).find('.spinner-border').hide();
                        if (response.status === false) {
                            $alert.html(response.message || 'Error creating treatment.').show();
                            return;
                        }
                        form.reset();
                        $(form).find('.is-invalid').removeClass('is-invalid');
                        closeModal('#quickAddTreatmentModal');

                        let newTreatmentId = response.treatment?.id || response.data?.id || response.id;
                        window._chainCallback('treatment', newTreatmentId, response, onTreatmentAdded);
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).find('.spinner-border').hide();
                        let errorMsg = parseAjaxError(xhr, 'Failed to create treatment.');
                        $alert.html(errorMsg).show();
                    }
                });
            }
        );

        // 3. Quick Add Doctor Submit & Validation
        setupValidation(
            '#quickAddDoctorForm',
            {
                fullname: {
                    required: true,
                    minlength: 2
                },
                email: {
                    required: true,
                    email: true
                },
                phone: {
                    required: true,
                    digits: true,
                    minlength: 10,
                    maxlength: 10
                },
                password: {
                    required: true,
                    minlength: 6
                }
            },
            {
                fullname: {
                    required: "Please enter doctor full name",
                    minlength: "Name must be at least 2 characters"
                },
                email: {
                    required: "Please enter email address",
                    email: "Please enter a valid email address"
                },
                phone: {
                    required: "Please enter phone number",
                    digits: "Please enter only digits",
                    minlength: "Phone number must be exactly 10 digits",
                    maxlength: "Phone number must be exactly 10 digits"
                },
                password: {
                    required: "Please enter a password",
                    minlength: "Password must be at least 6 characters"
                }
            },
            function() {
                let form = this;
                let $btn = $('#btnSaveQuickDoctor');
                let $alert = $('#quickDoctorAlert');
                $alert.hide().empty();

                let token = getToken();
                let branchId = getBranchId();

                let formData = new FormData(form);
                formData.append('branch_id', branchId);
                formData.append('role_id', 3); // Doctor Role ID is 3
                formData.append('address', 'Clinic Address');
                formData.append('city', 'Surat');
                formData.append('state', 'Gujarat');

                let defaultPermissions = [
                    { module_id: 1, view: true, create: true, update: true, delete: false },
                    { module_id: 6, view: true, create: true, update: true, delete: false },
                    { module_id: 7, view: true, create: true, update: true, delete: false }
                ];
                formData.append('permissions', JSON.stringify(defaultPermissions));

                $btn.prop('disabled', true).find('.spinner-border').show();

                $.ajax({
                    url: "{{ url('api/users') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { "Authorization": "Bearer " + token },
                    success: function(response) {
                        $btn.prop('disabled', false).find('.spinner-border').hide();
                        if (response.status === false) {
                            $alert.html(response.message || 'Error creating doctor.').show();
                            return;
                        }
                        form.reset();
                        $(form).find('.is-invalid').removeClass('is-invalid');
                        closeModal('#quickAddDoctorModal');

                        let newDoctorId = response.user?.id || response.data?.id;
                        let doctorName = response.user?.fullname || response.data?.fullname || '';
                        if (doctorName) {
                            doctorName = doctorName.charAt(0).toUpperCase() + doctorName.slice(1);
                        }

                        // Append and pre-select immediately in quickTreatmentDoctorSelect
                        if (newDoctorId) {
                            let $quickDocSelect = $('#quickTreatmentDoctorSelect');
                            if ($quickDocSelect.find(`option[value="${newDoctorId}"]`).length === 0) {
                                $quickDocSelect.append(`<option value="${newDoctorId}">${doctorName || 'Doctor'}</option>`);
                            }
                            $quickDocSelect.val(newDoctorId);
                        }

                        window._chainCallback('doctor', newDoctorId, response, onDoctorAdded);

                        // If Doctor modal was launched from inside Treatment Modal, reopen Treatment Modal and auto-select new doctor
                        if (doctorOpenedFromTreatment) {
                            doctorOpenedFromTreatment = false;
                            if (newDoctorId) {
                                $('#quickTreatmentDoctorSelect').val(newDoctorId);
                            }
                            setTimeout(function() {
                                if (newDoctorId) {
                                    $('#quickTreatmentDoctorSelect').val(newDoctorId);
                                }
                                showModal('#quickAddTreatmentModal');
                            }, 300);
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).find('.spinner-border').hide();
                        let errorMsg = parseAjaxError(xhr, 'Failed to create doctor.');
                        $alert.html(errorMsg).show();
                    }
                });
            }
        );
        // 4. Quick Add Machine Submit & Validation
        setupValidation(
            '#quickAddMachineForm',
            {
                name: {
                    required: true,
                    minlength: 2
                },
                price: {
                    required: true,
                    number: true,
                    min: 0
                }
            },
            {
                name: {
                    required: "Please enter machine title",
                    minlength: "Machine title must be at least 2 characters"
                },
                price: {
                    required: "Please enter price",
                    number: "Please enter a valid price",
                    min: "Price cannot be negative"
                }
            },
            function() {
                let form = this;
                let $btn = $('#btnSaveQuickMachine');
                let $alert = $('#quickMachineAlert');
                $alert.hide().empty();

                let token = getToken();
                let branchId = getBranchId();

                let formData = new FormData(form);
                formData.append('branch_id', branchId);

                $btn.prop('disabled', true).find('.spinner-border').show();

                $.ajax({
                    url: "{{ url('api/machines') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { "Authorization": "Bearer " + token },
                    success: function(response) {
                        $btn.prop('disabled', false).find('.spinner-border').hide();
                        if (response.status === false) {
                            $alert.html(response.message || 'Error creating machine.').show();
                            return;
                        }
                        form.reset();
                        $(form).find('.is-invalid').removeClass('is-invalid');
                        closeModal('#quickAddMachineModal');

                        let newMachineId = response.machine?.id || response.data?.id || response.id;
                        window._chainCallback('machine', newMachineId, response, onMachineAdded);
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).find('.spinner-border').hide();
                        let errorMsg = parseAjaxError(xhr, 'Failed to create machine.');
                        $alert.html(errorMsg).show();
                    }
                });
            }
        );

        // 5. Quick Add Diagnosis Submit & Validation
        setupValidation(
            '#quickAddDiagnosisForm',
            {
                name: {
                    required: true,
                    minlength: 2
                }
            },
            {
                name: {
                    required: "Please enter diagnosis name",
                    minlength: "Diagnosis name must be at least 2 characters"
                }
            },
            function() {
                let form = this;
                let $btn = $('#btnSaveQuickDiagnosis');
                let $alert = $('#quickDiagnosisAlert');
                $alert.hide().empty();

                let token = getToken();
                let branchId = getBranchId();

                let formData = new FormData(form);
                formData.append('branch_id', branchId);

                $btn.prop('disabled', true).find('.spinner-border').show();

                $.ajax({
                    url: "{{ url('api/diagnosis') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { "Authorization": "Bearer " + token },
                    success: function(response) {
                        $btn.prop('disabled', false).find('.spinner-border').hide();
                        if (response.status === false) {
                            $alert.html(response.message || 'Error creating diagnosis.').show();
                            return;
                        }
                        form.reset();
                        $(form).find('.is-invalid').removeClass('is-invalid');
                        closeModal('#quickAddDiagnosisModal');

                        let newDiagnosisId = response.diagnosis?.id || response.data?.id || response.id;
                        window._chainCallback('diagnosis', newDiagnosisId, response, onDiagnosisAdded);
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).find('.spinner-border').hide();
                        let errorMsg = parseAjaxError(xhr, 'Failed to create diagnosis.');
                        $alert.html(errorMsg).show();
                    }
                });
            }
        );

        // 6. Quick Add Symptom Submit & Validation
        setupValidation(
            '#quickAddSymptomForm',
            {
                name: {
                    required: true,
                    minlength: 2
                }
            },
            {
                name: {
                    required: "Please enter symptom name",
                    minlength: "Symptom name must be at least 2 characters"
                }
            },
            function() {
                let form = this;
                let $btn = $('#btnSaveQuickSymptom');
                let $alert = $('#quickSymptomAlert');
                $alert.hide().empty();

                let token = getToken();
                let branchId = getBranchId();

                let formData = new FormData(form);
                formData.append('branch_id', branchId);

                $btn.prop('disabled', true).find('.spinner-border').show();

                $.ajax({
                    url: "{{ url('api/symptoms/store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { "Authorization": "Bearer " + token },
                    success: function(response) {
                        $btn.prop('disabled', false).find('.spinner-border').hide();
                        if (response.status === false) {
                            $alert.html(response.message || 'Error creating symptom.').show();
                            return;
                        }
                        form.reset();
                        $(form).find('.is-invalid').removeClass('is-invalid');
                        closeModal('#quickAddSymptomModal');

                        let newSymptomId = response.symptom?.id || response.data?.id || response.id;
                        window._chainCallback('symptom', newSymptomId, response, onSymptomAdded);
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).find('.spinner-border').hide();
                        let errorMsg = parseAjaxError(xhr, 'Failed to create symptom.');
                        $alert.html(errorMsg).show();
                    }
                });
            }
        );

        // 7. Quick Add Therapy Submit & Validation
        setupValidation(
            '#quickAddTherapyForm',
            {
                name: {
                    required: true,
                    minlength: 2
                },
                duration_minutes: {
                    required: true,
                    number: true,
                    min: 1
                },
                cost: {
                    required: true,
                    number: true,
                    min: 0
                }
            },
            {
                name: {
                    required: "Please enter therapy name",
                    minlength: "Therapy name must be at least 2 characters"
                },
                duration_minutes: {
                    required: "Please enter duration in minutes",
                    number: "Please enter a valid number"
                },
                cost: {
                    required: "Please enter cost",
                    number: "Please enter a valid number",
                    min: "Cost cannot be negative"
                }
            },
            function() {
                let form = this;
                let $btn = $('#btnSaveQuickTherapy');
                let $alert = $('#quickTherapyAlert');
                $alert.hide().empty();

                let token = getToken();
                let branchId = getBranchId();

                let formData = new FormData(form);
                formData.append('branch_id', branchId);

                $btn.prop('disabled', true).find('.spinner-border').show();

                $.ajax({
                    url: "{{ url('api/therapies') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { "Authorization": "Bearer " + token },
                    success: function(response) {
                        $btn.prop('disabled', false).find('.spinner-border').hide();
                        if (response.status === false) {
                            $alert.html(response.message || 'Error creating therapy.').show();
                            return;
                        }
                        form.reset();
                        $(form).find('.is-invalid').removeClass('is-invalid');
                        closeModal('#quickAddTherapyModal');

                        let newTherapyId = response.data?.id || response.therapy?.id || response.id;
                        window._chainCallback('therapy', newTherapyId, response, onTherapyAdded);
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).find('.spinner-border').hide();
                        let errorMsg = parseAjaxError(xhr, 'Failed to create therapy.');
                        $alert.html(errorMsg).show();
                    }
                });
            }
        );

        // 8. Quick Add Supplier Submit & Validation
        setupValidation(
            '#quickAddSupplierForm',
            {
                name: {
                    required: true,
                    minlength: 2
                },
                contact_person: {
                    required: true
                },
                phone: {
                    required: true,
                    digits: true,
                    minlength: 10,
                    maxlength: 10
                },
                email: {
                    required: true,
                    email: true
                },
                address: {
                    required: true
                }
            },
            {
                name: {
                    required: "Please enter company name"
                },
                contact_person: {
                    required: "Please enter contact person"
                },
                phone: {
                    required: "Please enter phone number",
                    digits: "Please enter only digits",
                    minlength: "Phone number must be exactly 10 digits",
                    maxlength: "Phone number must be exactly 10 digits"
                },
                email: {
                    required: "Please enter email address",
                    email: "Please enter a valid email address"
                },
                address: {
                    required: "Please enter address"
                }
            },
            function() {
                let form = this;
                let $btn = $('#btnSaveQuickSupplier');
                let $alert = $('#quickSupplierAlert');
                $alert.hide().empty();

                let token = getToken();
                let branchId = getBranchId();

                let formData = new FormData(form);
                formData.append('branch_id', branchId);

                $btn.prop('disabled', true).find('.spinner-border').show();

                $.ajax({
                    url: "{{ url('api/suppliers') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { "Authorization": "Bearer " + token },
                    success: function(response) {
                        $btn.prop('disabled', false).find('.spinner-border').hide();
                        if (response.status === false) {
                            $alert.html(response.message || 'Error creating vendor.').show();
                            return;
                        }
                        form.reset();
                        $(form).find('.is-invalid').removeClass('is-invalid');
                        closeModal('#quickAddSupplierModal');

                        let newSupplierId = response.supplier?.id || response.data?.id || response.id;
                        window._chainCallback('supplier', newSupplierId, response, onSupplierAdded);
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).find('.spinner-border').hide();
                        let errorMsg = parseAjaxError(xhr, 'Failed to create supplier.');
                        $alert.html(errorMsg).show();
                    }
                });
            }
        );

    };

    // Auto-init with defaults when document is ready
    $(document).ready(function() {
        if (!window._quickAddModalsInitialized) {
            window._quickAddModalsInitialized = true;
            window.initQuickAddModals();
        }
    });
</script>
