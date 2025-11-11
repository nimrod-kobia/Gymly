<?php
/**
 * Contact Page - Get in touch with Gymly team
 */

require_once '../autoload.php';

$pageTitle = "Contact Us | Gymly";
include '../template/layout.php';
?>

<style>
    /* Contact Hero Section */
    .contact-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 4rem 0 3rem;
        margin-top: 56px;
    }
    
    /* Contact Cards */
    .contact-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 2rem;
        transition: all 0.3s ease;
        height: 100%;
        color: white;
    }
    
    .contact-card:hover {
        transform: translateY(-5px);
        border-color: rgba(102, 126, 234, 0.5);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }
    
    .contact-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    
    /* Contact Form */
    .contact-form-container {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 2.5rem;
    }
    
    .form-label {
        color: white;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .form-control, .form-select {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        padding: 0.75rem 1rem;
        border-radius: 8px;
    }
    
    .form-control:focus, .form-select:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: #667eea;
        color: white;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    }
    
    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }
    
    textarea.form-control {
        min-height: 120px;
    }
    
    .btn-send {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 600;
    }
    
    .btn-send:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    /* Alert Styling */
    .alert-success {
        background: rgba(34, 197, 94, 0.2);
        border-left: 4px solid #22c55e;
        color: white;
        border-radius: 8px;
    }
    
    .alert-danger {
        background: rgba(239, 68, 68, 0.2);
        border-left: 4px solid #ef4444;
        color: white;
        border-radius: 8px;
    }
</style>

<!-- Hero Section -->
<div class="contact-hero text-center text-white">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">Contact Us</h1>
        <p class="lead">Have questions? We'd love to hear from you.</p>
    </div>
</div>

<!-- Contact Methods -->
<section class="py-5 bg-black">
    <div class="container">
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="contact-card text-center">
                    <div class="contact-icon mx-auto">
                        <i class="bi bi-envelope-fill text-white"></i>
                    </div>
                    <h5 class="text-white mb-2">Email</h5>
                    <a href="mailto:info@gymly.com" class="text-light text-decoration-none">
                        info@gymly.com
                    </a>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="contact-card text-center">
                    <div class="contact-icon mx-auto">
                        <i class="bi bi-telephone-fill text-white"></i>
                    </div>
                    <h5 class="text-white mb-2">Phone</h5>
                    <a href="tel:+254700000000" class="text-light text-decoration-none">
                        +254 700 000 000
                    </a>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="contact-card text-center">
                    <div class="contact-icon mx-auto">
                        <i class="bi bi-geo-alt-fill text-white"></i>
                    </div>
                    <h5 class="text-white mb-2">Location</h5>
                    <p class="text-light mb-0">
                        Strathmore University<br>
                        Nairobi, Kenya
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="py-5 bg-black">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="contact-form-container">
                    <h3 class="text-white text-center mb-4">Send Us a Message</h3>
                    
                    <form id="contactForm" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       placeholder="Your name" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="your@email.com" required>
                            </div>
                            
                            <div class="col-12">
                                <label for="subject" class="form-label">Subject</label>
                                <select class="form-select" id="subject" name="subject" required>
                                    <option value="">Choose...</option>
                                    <option value="General Inquiry">General Inquiry</option>
                                    <option value="Technical Support">Technical Support</option>
                                    <option value="Feedback">Feedback</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" 
                                          placeholder="Your message..." required></textarea>
                            </div>
                            
                            <div class="col-12">
                                <div id="formResult"></div>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-send w-100">
                                    <i class="bi bi-send-fill me-2"></i>Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.getElementById('contactForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const resultDiv = document.getElementById('formResult');
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Sending...';
        
        try {
            const response = await fetch('../handlers/contactHandler.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                resultDiv.innerHTML = `
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill me-2"></i>${result.message}
                    </div>
                `;
                form.reset();
            } else {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>${result.message}
                    </div>
                `;
            }
        } catch (error) {
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>An error occurred. Please try again.
                </div>
            `;
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-send-fill me-2"></i>Send Message';
        }
    });
</script>

<?php include '../template/footer.php'; ?>
