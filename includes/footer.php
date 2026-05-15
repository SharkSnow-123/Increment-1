<!-- ========== ALERT MODAL ========== -->
<div id="alertModal" style="display:none; position:fixed; top:0; left:0; width:100%; 
     height:100%; background:rgba(0,0,0,0.45); z-index:9999; 
     align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; padding:2rem; width:320px; 
                text-align:center; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
        <div id="alertIcon" style="font-size:40px; margin-bottom:12px;"></div>
        <p id="alertTitle" style="font-weight:600; font-size:16px; margin:0 0 6px;"></p>
        <p id="alertMessage" style="font-size:14px; color:#666; margin:0 0 1.5rem; line-height:1.5;"></p>
        <button onclick="closeModal('alertModal')" 
                style="padding:10px 28px; border-radius:8px; border:none; 
                       background:#333; color:#fff; font-size:14px; cursor:pointer;">
            OK
        </button>
    </div>
</div>

<!-- ========== CONFIRM MODAL ========== -->
<div id="confirmModal" style="display:none; position:fixed; top:0; left:0; width:100%; 
     height:100%; background:rgba(0,0,0,0.45); z-index:9999; 
     align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; padding:2rem; width:320px; 
                text-align:center; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
        <div style="font-size:40px; margin-bottom:12px;">
            <i class="ti ti-alert-triangle" style="color:#e67e22;"></i>
        </div>
        <p id="confirmTitle" style="font-weight:600; font-size:16px; margin:0 0 6px;"></p>
        <p id="confirmMessage" style="font-size:14px; color:#666; margin:0 0 1.5rem; line-height:1.5;"></p>
        <div style="display:flex; gap:10px; justify-content:center;">
            <button onclick="closeModal('confirmModal')" 
                    style="padding:10px 20px; border-radius:8px; border:1px solid #ccc; 
                           background:#fff; font-size:14px; cursor:pointer;">
                Go back
            </button>
            <button id="confirmYesBtn" 
                    style="padding:10px 20px; border-radius:8px; border:none; 
                           background:#e74c3c; color:#fff; font-size:14px; cursor:pointer;">
            </button>
        </div>
    </div>
</div>

<!-- ========== MODAL JAVASCRIPT ========== -->
<script>
let confirmCallback = null;

function showAlert(type, title, message) {
    const icons = {
        success : '<i class="ti ti-circle-check" style="color:#27ae60;"></i>',
        error   : '<i class="ti ti-circle-x" style="color:#e74c3c;"></i>',
        info    : '<i class="ti ti-info-circle" style="color:#2980b9;"></i>',
        warning : '<i class="ti ti-alert-triangle" style="color:#e67e22;"></i>'
    };
    document.getElementById('alertIcon').innerHTML = icons[type] || icons.info;
    document.getElementById('alertTitle').textContent = title;
    document.getElementById('alertMessage').textContent = message;
    document.getElementById('alertModal').style.display = 'flex';
}

function showConfirm(title, message, yesLabel, callback) {
    document.getElementById('confirmTitle').textContent = title;
    document.getElementById('confirmMessage').textContent = message;
    document.getElementById('confirmYesBtn').textContent = yesLabel;
    confirmCallback = callback;
    document.getElementById('confirmModal').style.display = 'flex';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
    if (id === 'confirmModal') confirmCallback = null;
}

document.getElementById('confirmYesBtn').addEventListener('click', function() {
    closeModal('confirmModal');
    if (confirmCallback) confirmCallback();
});
</script>

</body>
</html>