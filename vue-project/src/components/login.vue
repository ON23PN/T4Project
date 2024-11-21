<template>
    <div v-if="isVisible" class="modal-overlay" @click.self="close">
        <div class="modal-content">
            <!-- Titel, abhängig vom Formularmodus (Login oder Registrierung) -->
            <h2>{{ isRegistering ? 'Jetzt Registrieren' : 'Login' }}</h2>

            <!-- Login Formular -->
            <form v-if="!isRegistering" @submit.prevent="submitLogin">
                <div class="form-group">
                    <input type="text" id="username" placeholder="Benutzername" v-model="username" required />
                </div>
                <div class="form-group">
                    <input type="password" id="password" placeholder="Passwort" v-model="password" required />
                </div>
                <button type="submit" class="submit-btn">Anmelden</button>
                <p>Noch kein Konto? <a href="#" @click.prevent="toggleForm">Registrieren</a></p>
            </form>

            <!-- Registrierungsformular -->
            <form v-if="isRegistering" @submit.prevent="submitRegister">
                <div class="form-group">
                    <input type="text" id="firstname" placeholder="Vorname" v-model="firstname" required />
                </div>
                <div class="form-group">
                    <input type="text" id="lastname" placeholder="Nachname" v-model="lastname" required />
                </div>
                <div class="form-group">
                    <input type="text" id="username" placeholder="Benutzername" v-model="username" required />
                </div>
                <div class="form-group">
                    <input type="email" id="email" placeholder="E-Mail" v-model="email" required />
                </div>
                <div class="form-group">
                    <input type="password" id="password" placeholder="Passwort" v-model="password" required />
                </div>
                <div class="form-group">
                    <input type="password" id="confirmPassword" placeholder="Passwort bestätigen"
                        v-model="confirmPassword" required />
                </div>
                <button type="submit" class="submit-btn">Registrieren</button>
                <p>Bereits ein Konto? <a href="#" @click.prevent="toggleForm">Anmelden</a></p>
            </form>

            <!-- Button zum Schließen des Modals -->
            <button type="button" class="close-btn" @click="close">Schließen</button>
            <!-- Anzeige einer Fehlermeldung, wenn beim Login oder der Registrierung ein Fehler auftritt -->
            <p v-if="loginError" class="error-message">{{ loginError }}</p>
        </div>

        <!-- Bestätigungspopup -->
        <div v-if="showSuccessPopup" class="success-popup">
            <p>Login erfolgreich! 🎉</p>
            <button @click="closeSuccessPopup">OK</button>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const email = ref('');
const password = ref('');
const confirmPassword = ref('');
const firstname = ref('');
const lastname = ref('');
const username = ref('');
const loginError = ref('');
const isVisible = ref(true);
const isRegistering = ref(false);
const showSuccessPopup = ref(false);

// Toggle between login and register forms
const toggleForm = () => {
    isRegistering.value = !isRegistering.value;
};

// Handle Login Submission
const submitLogin = async () => {
    const formData = new FormData();
    formData.append('username', username.value);
    formData.append('password', password.value);

    try {
        const response = await fetch('http://localhost/Projekt/vue-project/backend/login.php', {
            method: 'POST',
            body: formData,
            credentials: 'include',
        });

        const result = await response.json();
        if (result.message === 'Login erfolgreich!') {
            showSuccessPopup.value = true;
            close();
            // You can emit an event to notify parent component about the login success
            emit('login', result.user);
        } else {
            loginError.value = result.message || 'Login fehlgeschlagen';
        }
    } catch (error) {
        loginError.value = 'Ein Fehler ist aufgetreten.';
    }
};

// Handle Registration Submission
const submitRegister = async () => {
    if (password.value !== confirmPassword.value) {
        loginError.value = 'Passwörter stimmen nicht überein.';
        return;
    }

    const formData = new FormData();
    formData.append('firstname', firstname.value);
    formData.append('lastname', lastname.value);
    formData.append('username', username.value);
    formData.append('email', email.value);
    formData.append('password', password.value);

    try {
        const response = await fetch('http://localhost/Projekt/vue-project/backend/register.php', {
            method: 'POST',
            body: formData,
            credentials: 'include',
        });

        const result = await response.json();
        if (result.message === 'Registrierung erfolgreich!') {
            toggleForm();
        } else {
            loginError.value = result.message || 'Registrierung fehlgeschlagen';
        }
    } catch (error) {
        loginError.value = 'Ein Fehler ist aufgetreten.';
    }
};

// Close the modal
const close = () => {
    isVisible.value = false;
    emit('close');
};

// Close the success popup
const closeSuccessPopup = () => {
    showSuccessPopup.value = false;
    close();
};

const emit = defineEmits(['login', 'close']);
</script>

<style scoped>
a {
    color: rgb(25, 90, 130);
}

a:hover {
    color: rgb(15, 46, 65);
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1050;
}

.modal-content {
    background-color: #ffffff;
    padding: 2rem;
    border-radius: 8px;
    width: 90%;
    max-width: 400px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    text-align: center;
    z-index: 1060;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group input {
    width: 100%;
    padding: 0.75rem;
    border-radius: 4px;
    border: 1px solid #ddd;
    box-sizing: border-box;
    font-size: 16px;
}

.submit-btn {
    font-size: 16px;
    color: rgb(25, 90, 130);
    text-decoration: none;
    padding: 10px 20px;
    border: 1px solid rgb(25, 90, 130);
    border-radius: 4px;
    background-color: #fff;
    cursor: pointer;
    transition: background-color 0.3s, color 0.3s;
}

.submit-btn:hover {
    background-color: rgb(25, 90, 130);
    color: white;
}

.close-btn {
    background-color: transparent;
    border: none;
    color: #777;
    font-size: 0.9rem;
    cursor: pointer;
    margin-top: 1rem;
    display: block;
    width: 100%;
}

.error-message {
    color: red;
    margin-top: 1rem;
    font-size: 0.9rem;
}

.success-popup {
    position: fixed;
    top: 20%;
    left: 50%;
    transform: translateX(-50%);
    background-color: #4caf50;
    color: white;
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}
</style>