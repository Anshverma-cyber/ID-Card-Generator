// =================== GLOBALS ===================
let currentVisitorId = null;  

// =================== PHOTO UPLOAD ===================
const photoInput = document.getElementById('photo');
const photoPlaceholder = document.querySelector('.photo-placeholder');

if (photoInput) {
  photoInput.addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (event) {
        photoPlaceholder.innerHTML = `<img src="${event.target.result}" 
          alt="Visitor Photo" 
          style="width:100px; height:100px; border-radius:50%;">`;
      };
      reader.readAsDataURL(file);
    }
  });
}

// =================== MAIN LOGIC ===================
document.addEventListener('DOMContentLoaded', function() {
  // Inputs
  const card = document.getElementById('card');
  const cardTitleInput = document.getElementById('cardTitle');
  const cardTitleDisplay = document.getElementById('cardTitleDisplay');
  const nameInput = document.getElementById('name');
  const companyInput = document.getElementById('company');
  const purposeInput = document.getElementById('purpose');
  const purposeDisplay = document.getElementById('purposeDisplay');
  const contactInput = document.getElementById('contact');
  const contactDisplay = document.getElementById('contactDisplay');
  const validUntilInput = document.getElementById('validUntil');
  const validUntilDisplay = document.getElementById('validUntilDisplay');
  const additionalInfoInput = document.getElementById('additionalInfo');
  const additionalInfoDisplay = document.getElementById('additionalInfoDisplay');
  const includeQRCheckbox = document.getElementById('includeQR');
  const qrCodeDisplay = document.getElementById('qrCodeDisplay');
  const signatureDisplay = document.getElementById('signatureDisplay');
  const downloadBtn = document.getElementById('downloadBtn');
  const saveTemplateBtn = document.getElementById('saveTemplateBtn');
  const resetBtn = document.getElementById('resetBtn');
  const generateBtn = document.getElementById('generateBtn');
  const colorOptions = document.querySelectorAll('.color-option');
  const templates = document.querySelectorAll('.template');
  const cardHeader = document.querySelector('.card-header');

  // ---------- Date default ----------
  const today = new Date();
  const tomorrow = new Date(today);
  tomorrow.setDate(tomorrow.getDate() + 1);
  if (validUntilInput) validUntilInput.valueAsDate = tomorrow;
  updateValidUntilDisplay();

  // ---------- Input Listeners ----------
  if (cardTitleInput) cardTitleInput.addEventListener('input', () => cardTitleDisplay.textContent = cardTitleInput.value.toUpperCase());
  if (nameInput) nameInput.addEventListener('input', () => document.querySelector('.name').textContent = nameInput.value || 'John Doe');
  if (companyInput) companyInput.addEventListener('input', () => document.querySelector('.college').textContent = companyInput.value || 'College Name');
  if (purposeInput) purposeInput.addEventListener('input', () => purposeDisplay.textContent = purposeInput.value || 'B.Sc');
  if (contactInput) contactInput.addEventListener('input', () => contactDisplay.textContent = contactInput.value || '01/01/2000');
  if (validUntilInput) validUntilInput.addEventListener('change', updateValidUntilDisplay);
  if (additionalInfoInput) additionalInfoInput.addEventListener('input', () => additionalInfoDisplay.textContent = additionalInfoInput.value || 'Contact No.');
  if (includeQRCheckbox) includeQRCheckbox.addEventListener('change', () => qrCodeDisplay.style.display = includeQRCheckbox.checked ? 'block' : 'none');
  if (generateBtn) generateBtn.addEventListener('click', generateCard);
  if (resetBtn) resetBtn.addEventListener('click', resetForm);
  if (downloadBtn) downloadBtn.addEventListener('click', downloadCard);
  if (saveTemplateBtn) saveTemplateBtn.addEventListener('click', saveTemplate);

  // ---------- Color Selector ----------
  colorOptions.forEach(opt => opt.addEventListener('click', function() {
    document.querySelector('.color-option.selected').classList.remove('selected');
    this.classList.add('selected');
    cardHeader.style.backgroundColor = this.dataset.color;
  }));

  // ---------- Template Selector ----------
  templates.forEach(t => t.addEventListener('click', function() {
    document.querySelector('.template.selected').classList.remove('selected');
    this.classList.add('selected');
    applyTemplate(this.dataset.template);
  }));
  applyTemplate('1');

  // =================== FUNCTIONS ===================

  function updateValidUntilDisplay() {
    if (validUntilInput && validUntilInput.value) {
      const d = new Date(validUntilInput.value);
      validUntilDisplay.textContent = d.toLocaleDateString('en-GB');
    }
  }

  function applyTemplate(n) {
    cardHeader.style.background = '';
    if (n === '1') cardHeader.style.backgroundColor = '#4361ee';
    if (n === '2') cardHeader.style.background = 'linear-gradient(45deg, #4361ee, #3a0ca3)';
    if (n === '3') cardHeader.style.backgroundColor = '#3f37c9';
    if (n === '4') cardHeader.style.background = 'linear-gradient(45deg, #4895ef, #4361ee)';
    if (n === '5') cardHeader.style.backgroundColor = '#4cc9f0';
  }

  function generateCard() {
    const qrData = `Student: ${nameInput.value || 'John Doe'}\nCollege: ${companyInput.value || 'College Name'}\nCourse: ${purposeInput.value || 'B.Sc'}\nValid Until: ${validUntilDisplay.textContent}`;
    if (qrCodeDisplay) {
      const img = qrCodeDisplay.querySelector('img');
      if (img) img.src = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(qrData)}`;
    }
    signatureDisplay.style.display = nameInput.value ? 'block' : 'none';
  }

  function resetForm() {
    cardTitleInput.value = 'STUDENT ID CARD';
    nameInput.value = companyInput.value = purposeInput.value = contactInput.value = additionalInfoInput.value = '';
    validUntilInput.valueAsDate = tomorrow;
    includeQRCheckbox.checked = true;
    updateValidUntilDisplay();
    applyTemplate('1');
    signatureDisplay.style.display = 'none';
    document.querySelector('.photo-placeholder').innerHTML = '<i class="fas fa-user"></i>';
    document.getElementById('logoDisplay').style.display = 'none';
  }

  // ---------- SAVE TO DATABASE ----------
  function saveTemplate() {
    const templateData = {
      title: cardTitleInput.value,
      name: nameInput.value,
      company: companyInput.value,
      purpose: purposeInput.value,
      contact: contactInput.value,
      validUntil: validUntilInput.value,
      additionalInfo: additionalInfoInput.value,
      includeQR: includeQRCheckbox.checked ? 1 : 0,
      headerColor: window.getComputedStyle(document.querySelector('.card-header')).backgroundColor,
      template: "1"
    };

    fetch("save_visitor.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(templateData)
    })
    .then(res => res.json())
    .then(data => {
      console.log("Save Response:", data);
      alert(data.message);
      if (data.status === "success") {
        currentVisitorId = data.insert_id;   // ✅ FIXED
        console.log("Visitor saved with ID:", currentVisitorId);
      }
    })
    .catch(err => console.error("Save error:", err));
  }

  // ---------- DOWNLOAD & UPDATE ----------
  function downloadCard() {
    html2canvas(card, { scale: 3, useCORS: true, backgroundColor: null }).then(canvas => {
      // download as image
      const link = document.createElement("a");
      link.download = "student-id-card.png";
      link.href = canvas.toDataURL("image/png");
      link.click();

      // update DB (if saved)
      if (currentVisitorId) {
        fetch("update_download.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: "id=" + currentVisitorId
        })
        .then(res => res.text())
        .then(txt => console.log("DB Update:", txt))
        .catch(err => console.error("Update error:", err));
      } else {
        console.warn("⚠️ No visitor ID found. Please save before download.");
      }
    });
  }

});
