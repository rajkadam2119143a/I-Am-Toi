class CSVUploadComponent {
	constructor() {
		this.uploadBox = document.getElementById('uploadBox');
		this.fileInput = document.getElementById('fileInput');
		this.filesPreview = document.getElementById('filesPreview');
		this.filesList = document.getElementById('filesList');
		this.uploadProgress = document.getElementById('uploadProgress');
		this.uploadComplete = document.getElementById('uploadComplete');
		this.addMoreBtn = document.getElementById('addMoreBtn');
		this.newUploadBtn = document.getElementById('newUploadBtn');
		this.viewFilesBtn = document.getElementById('viewFilesBtn');
		this.importResult = document.getElementById('importResult');

		this.maxFileSize = 5 * 1024 * 1024; // 5MB for CSV files
		this.allowedTypes = ['text/csv', 'application/csv'];
		this.currentFile = null;

		this.init();
	}

	init() {
		this.setupEventListeners();
	}

	setupEventListeners() {
		// Upload box events
		this.uploadBox.addEventListener('click', () => {
			this.fileInput.click();
		});

		this.fileInput.addEventListener('change', (e) => {
			this.handleFile(e.target.files[0]);
		});

		// Drag and drop events
		this.uploadBox.addEventListener('dragover', (e) => {
			e.preventDefault();
			this.uploadBox.classList.add('dragover');
		});

		this.uploadBox.addEventListener('dragleave', (e) => {
			e.preventDefault();
			this.uploadBox.classList.remove('dragover');
		});

		this.uploadBox.addEventListener('drop', (e) => {
			e.preventDefault();
			this.uploadBox.classList.remove('dragover');
			this.handleFile(e.dataTransfer.files[0]);
		});

		// Action buttons
		this.addMoreBtn.addEventListener('click', () => {
			this.fileInput.click();
		});

		this.newUploadBtn.addEventListener('click', () => {
			this.startNewUpload();
		});

		this.viewFilesBtn.addEventListener('click', () => {
			this.viewEmployees();
		});

		// Prevent default drag behaviors
		['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
			document.addEventListener(eventName, (e) => {
				e.preventDefault();
				e.stopPropagation();
			});
		});
	}

	handleFile(file) {
		if (!file) return;

		if (this.validateFile(file)) {
			this.currentFile = file;
			this.showFilePreview(file);
			this.uploadFile(file);
		}
	}

	validateFile(file) {
		// Check file type
		const fileName = file.name.toLowerCase();
		if (!fileName.endsWith('.csv')) {
			this.showError('Please select a valid CSV file.');
			return false;
		}

		// Check file size
		if (file.size > this.maxFileSize) {
			this.showError(`${file.name}: File size must be less than 5MB.`);
			return false;
		}

		return true;
	}

	showFilePreview(file) {
		const fileSize = this.formatFileSize(file.size);
		const fileItem = `
<div class="file-item">
<div class="file-preview">
<svg width="60" height="60" viewBox="0 0 24 24" fill="none">
<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="currentColor" stroke-width="2"/>
<polyline points="14,2 14,8 20,8" stroke="currentColor" stroke-width="2"/>
<line x1="16" y1="13" x2="8" y2="13" stroke="currentColor" stroke-width="2"/>
<line x1="16" y1="17" x2="8" y2="17" stroke="currentColor" stroke-width="2"/>
<polyline points="10,9 9,9 8,9" stroke="currentColor" stroke-width="2"/>
</svg>
</div>
<div class="file-info">
<div class="file-name">${file.name}</div>
<div class="file-size">${fileSize}</div>
</div>
<div class="file-status">
<span class="status-icon status-uploading">⏳</span>
</div>
</div>
`;

		this.filesList.innerHTML = fileItem;
		this.filesPreview.style.display = 'block';
		this.uploadBox.classList.add('uploading');
	}

	uploadFile(file) {
		const formData = new FormData();
		formData.append('action', 'employee_csv_import');
		formData.append('employee_csv_nonce', this.getNonce());
		formData.append('csv_file', file);

		// Debug information
		console.log('Uploading file:', file.name);
		console.log('AJAX URL:', this.getAjaxUrl());
		console.log('Nonce:', this.getNonce());

		// Simulate progress
		let progress = 0;
		const progressInterval = setInterval(() => {
			progress += Math.random() * 15;
			if (progress > 90) progress = 90;
			this.updateProgress(progress);
		}, 200);

		// jQuery AJAX call
		jQuery.ajax({
			url: this.getAjaxUrl(),
			type: 'POST',
			data: formData,
			contentType: false,
			processData: false,
			success: (response) => {
				clearInterval(progressInterval);
				this.updateProgress(100);

				console.log('Upload success response:', response);

				setTimeout(() => {
					this.uploadBox.classList.remove('uploading');
					this.filesPreview.style.display = 'none';
					this.uploadComplete.style.display = 'block';

					// Check if response contains errors
					if (response.includes('Error') || response.includes('Failed') || response.includes('Security check failed') || response.includes('Permission denied')) {
						this.importResult.innerHTML = response;
						this.showError('Import completed with errors. Please check the details above.');
					} else if (response.includes('Import complete')) {
						this.importResult.innerHTML = response;
						this.showSuccess('Employee import completed successfully!');
					} else {
						this.importResult.innerHTML = response;
						this.showSuccess('Import completed successfully!');
					}

				}, 500);
			},
			error: (xhr, status, error) => {
				clearInterval(progressInterval);
				this.uploadBox.classList.remove('uploading');
				console.error('Upload error:', {xhr, status, error});
				
				// Try to extract error message from response
				let errorMessage = 'Upload failed';
				if (xhr.responseText) {
					try {
						// Check if response contains HTML error message
						if (xhr.responseText.includes('Error') || xhr.responseText.includes('Failed')) {
							errorMessage = xhr.responseText;
						} else {
							errorMessage = xhr.responseText;
						}
					} catch (e) {
						errorMessage = 'Upload failed: ' + error;
					}
				} else {
					errorMessage = 'Upload failed: ' + error;
				}
				
				this.showError(errorMessage);
				
				// Also display in result area
				this.importResult.innerHTML = '<div class="error-message">' + errorMessage + '</div>';
			}
		});
	}

	updateProgress(percentage) {
		const progressText = document.querySelector('.progress-text');
		const progressBar = document.querySelector('.progress-bar');

		if (progressText && progressBar) {
			progressText.innerHTML = Math.round(percentage) + '%';

			const circumference = 2 * Math.PI * 25;
			const offset = circumference - (percentage / 100) * circumference;
			progressBar.style.strokeDashoffset = offset;
		}
	}

	showError(message) {
		const errorDiv = document.createElement('div');
		errorDiv.className = 'error-notification';
		errorDiv.innerHTML = message;
		document.body.appendChild(errorDiv);

		setTimeout(() => {
			errorDiv.remove();
		}, 5000);
	}

	showSuccess(message) {
		const successDiv = document.createElement('div');
		successDiv.className = 'success-notification';
		successDiv.innerHTML = message;
		document.body.appendChild(successDiv);

		setTimeout(() => {
			successDiv.remove();
		}, 5000);
	}

	startNewUpload() {
		this.uploadComplete.style.display = 'none';
		this.uploadBox.classList.remove('uploading');
		this.fileInput.value = '';
		this.filesList.innerHTML = '';
		this.currentFile = null;

		// Reset progress
		this.updateProgress(0);
	}

	viewEmployees() {
		window.location.href = this.getAdminUrl() + 'edit.php?post_type=employee';
	}

	formatFileSize(bytes) {
		if (bytes === 0) return '0 Bytes';

		const k = 1024;
		const sizes = ['Bytes', 'KB', 'MB', 'GB'];
		const i = Math.floor(Math.log(bytes) / Math.log(k));

		return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
	}

	getNonce() {
		// Get nonce from a data attribute or generate it
		const nonceElement = document.querySelector('meta[name="employee_csv_nonce"]');
		if (!nonceElement) {
			console.error('CSV import nonce not found. Please ensure the template includes the nonce meta tag.');
			return '';
		}
		return nonceElement.getAttribute('content') || '';
	}

	getAjaxUrl() {
		// Get AJAX URL from WordPress
		return window.ajaxurl || '/wp-admin/admin-ajax.php';
	}

	getAdminUrl() {
		// Get admin URL from WordPress
		return window.adminUrl || '/wp-admin/';
	}
}

// Initialize the component when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
	// Check if we're on the admin dashboard page
	if (document.getElementById('uploadBox')) {
		window.csvUpload = new CSVUploadComponent();
	}
});

// Export for potential module use
if (typeof module !== 'undefined' && module.exports) {
	module.exports = CSVUploadComponent;
}
