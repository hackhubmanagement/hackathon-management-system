/* Modern Auth Script for HackHub */

let currentLoginType = 'student';

function switchTab(type, e) {
    if (e) e.preventDefault();
    currentLoginType = type;

    document.querySelectorAll('.tab-btn').forEach(tab => tab.classList.remove('active'));
    const clickedTab = e?.target.closest('.tab-btn');
    if (clickedTab) {
        clickedTab.classList.add('active');
    }
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
        
        let result;
        try {
            result = await response.json();
        } catch (parseError) {
            const text = await response.text();
            result = { type: 'invalid', message: text || 'Invalid email or password' };
        }
        
        if (result.type && result.type !== 'invalid') {
            localStorage.setItem('userData', JSON.stringify(result));
            localStorage.setItem('userType', result.type);
            localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
            
            if (result.type === 'student') {
                window.location.href = 'student.html';
            } else if (result.type === 'admin') {
                window.location.href = 'admin.html';
            } else if (result.type === 'judge') {
                window.location.href = 'judge.html';
            }
        } else {
            alert(result.message || 'Invalid email or password');
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
