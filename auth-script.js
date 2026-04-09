/* Modern Auth Script for HackHub */

let currentLoginType = 'student';

function switchTab(type, e) {
    if (e) e.preventDefault();
    currentLoginType = type;
    
    document.querySelectorAll('.auth-tab').forEach(tab => tab.classList.remove('active'));
    event.target.closest('.auth-tab')?.classList.add('active');
}

function showLogin(e) {
    if (e) e.preventDefault();
    document.getElementById('loginSection').style.display = 'block';
    document.getElementById('registrationSection').style.display = 'none';
    document.getElementById('forgotPasswordSection').style.display = 'none';
}

function showRegistration(e) {
    if (e) e.preventDefault();
    document.getElementById('loginSection').style.display = 'none';
    document.getElementById('registrationSection').style.display = 'block';
    document.getElementById('forgotPasswordSection').style.display = 'none';
}

function showForgotPassword(e) {
    if (e) e.preventDefault();
    document.getElementById('loginSection').style.display = 'none';
    document.getElementById('registrationSection').style.display = 'none';
    document.getElementById('forgotPasswordSection').style.display = 'block';
}

async function handleLogin(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    try {
        let formData = new FormData();
        formData.append('email', email);
        formData.append('password', password);
        formData.append('user_type', currentLoginType);
        
        let response = await fetch('login.php', {
            method: 'POST',
            body: formData
        });
        
        let result = await response.text().trim();
        
        if (result === 'success') {
            // Fetch user data
            const userResponse = await fetch(`login.php?get_user=${email}&type=${currentLoginType}`);
            const userData = await userResponse.json();
            
            localStorage.setItem('currentUser', JSON.stringify(userData));
            localStorage.setItem('userType', currentLoginType);
            localStorage.setItem('theme', 'light');
            
            if (currentLoginType === 'student') {
                window.location.href = 'student-new.html';
            } else {
                window.location.href = 'admin-new.html';
            }
        } else {
            alert('Invalid email or password');
        }
    } catch (error) {
        console.error('Login error:', error);
        alert('Login failed. Please try again.');
    }
}

async function handleRegistration(e) {
    e.preventDefault();
    
    const name = document.getElementById('regName').value;
    const email = document.getElementById('regEmail').value;
    const password = document.getElementById('regPassword').value;
    const confirmPassword = document.getElementById('regConfirmPassword').value;
    const college = document.getElementById('regCollegeName').value;
    
    if (password !== confirmPassword) {
        alert('Passwords do not match!');
        return;
    }
    
    try {
        let formData = new FormData();
        formData.append('name', name);
        formData.append('email', email);
        formData.append('password', password);
        formData.append('college_name', college);
        
        let response = await fetch('register.php', {
            method: 'POST',
            body: formData
        });
        
        let result = await response.text().trim();
        
        if (result === 'success') {
            alert('Registration successful! You can now login.');
            showLogin();
            document.getElementById('email').value = email;
        } else {
            alert('Registration failed: ' + result);
        }
    } catch (error) {
        console.error('Registration error:', error);
        alert('Registration failed. Please try again.');
    }
}

async function handleForgotPassword(e) {
    e.preventDefault();
    
    const email = document.getElementById('forgotEmail').value;
    
    try {
        let formData = new FormData();
        formData.append('email', email);
        
        let response = await fetch('forgot_password.php', {
            method: 'POST',
            body: formData
        });
        
        let result = await response.text().trim();
        
        if (result === 'success') {
            alert('Reset link sent to your email!');
            showLogin();
        } else {
            alert('Email not found');
        }
    } catch (error) {
        console.error('Forgot password error:', error);
        alert('Request failed. Please try again.');
    }
}

function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
}

// Load dark mode preference on page load
window.addEventListener('DOMContentLoaded', function() {
    if (localStorage.getItem('darkMode') === 'true') {
        document.body.classList.add('dark-mode');
    }
});
