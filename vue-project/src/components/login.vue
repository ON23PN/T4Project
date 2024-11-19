<template>
    <!-- Modal, das angezeigt wird, wenn das Login- oder Registrierungsformular sichtbar ist -->
    <div v-if="isVisible" class="modal-overlay" @click.self="close">
        <div class="modal-content">
            <!-- Der Titel ändert sich je nachdem, ob der Nutzer sich einloggen oder registrieren möchte -->
            <h2>{{ isRegistering ? 'Jetzt Registrieren' : 'Login' }}</h2>

            <!-- Login Formular -->
            <form v-if="!isRegistering" @submit.prevent="submitLogin">
                <div class="form-group">
                    <input type="email" id="email" placeholder="E-Mail" v-model="email" required />
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
                    <input type="email" id="email" placeholder="E-Mail" v-model="email" required />
                </div>
                <div class="form-group">
                    <input type="password" id="password" placeholder="Passwort" v-model="password" required />
                </div>
                <div class="form-group">
                    <input type="password" id="confirmPassword" placeholder="Passwort wiederholen"
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

<script>
export default {
    name: "Login",
    data() {
        return {
            email: "", // E-Mail des Benutzers
            password: "", // Passwort des Benutzers
            confirmPassword: "", // Passwortbestätigung, nur für Registrierung
            firstname: "", // Vorname, nur für Registrierung
            lastname: "", // Nachname, nur für Registrierung
            loginError: "", // Fehlernachricht bei fehlerhaften Anmeldedaten
            isVisible: true, // Steuert, ob das Modal sichtbar ist
            isRegistering: false, // Wechselt zwischen Login und Registrierung
            showSuccessPopup: false, // Steuert das Anzeigen des Erfolgspopups
        };
    },
    methods: {
        // Wechselt zwischen dem Login- und dem Registrierungsmodus
        toggleForm() {
            this.isRegistering = !this.isRegistering;
        },

        // Logik für den Login
        async submitLogin() {
            const formData = new FormData();
            formData.append("email", this.email);
            formData.append("password", this.password);

            try {
                // Anfrage an den Server senden, um den Login zu verarbeiten
                const response = await fetch('http://localhost/Projekt/vue-project/backend/login.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'include', // Wichtige Option, um Cookies zu senden
                });

                // Logge die Antwort, um zu sehen, was vom Backend zurückkommt
                const responseText = await response.text(); // Antwort als Text bekommen
                console.log("Antwort Text: ", responseText);

                // Versuch, die Antwort als JSON zu parsen
                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (error) {
                    console.error("Fehler beim Parsen der Antwort:", error);
                    this.loginError = "Fehler beim Verarbeiten der Antwort.";
                    return;
                }

                // Überprüfe, was vom Backend zurückkommt
                console.log("Antwort JSON: ", result);

                if (result.message === "Login erfolgreich!") {
                    console.log("Login erfolgreich");
                    this.showSuccessPopup = true; // Zeige das Popup an
                    this.close(); // Schließt das Login-Popup nach erfolgreichem Login
                } else {
                    // Zeigt eine Fehlermeldung, wenn der Login fehlgeschlagen ist
                    this.loginError = result.message || "Login fehlgeschlagen";
                }
            } catch (error) {
                // Fehlerbehandlung für den Fall, dass die Anfrage nicht ausgeführt werden kann
                console.error("Fehler beim Login:", error);
                this.loginError = "Ein Fehler ist aufgetreten.";
            }
        },

        // Funktion zum Schließen des Modals
        close() {
            this.isVisible = false; // Popup schließen
            this.$emit('close'); // Event auslösen, um das Modal zu schließen (wenn nötig)
        },

        // Funktion für die Registrierung des Benutzers
        async submitRegister() {
            // Überprüft, ob die Passwörter übereinstimmen
            if (this.password !== this.confirmPassword) {
                this.loginError = "Passwörter stimmen nicht überein.";
                return;
            }

            // Formulardaten für die Registrierung sammeln
            const formData = new FormData();
            formData.append("firstname", this.firstname);
            formData.append("lastname", this.lastname);
            formData.append("email", this.email);
            formData.append("password", this.password);

            try {
                // Anfrage an den Server senden, um den Benutzer zu registrieren
                const response = await fetch('http://localhost/Projekt/vue-project/backend/register.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'include',
                });

                const result = await response.json();

                if (result.message === "Registrierung erfolgreich!") {
                    console.log("Registrierung erfolgreich");
                    this.toggleForm(); // Wechsel zu Login nach erfolgreicher Registrierung
                } else {
                    // Zeigt eine Fehlermeldung an, wenn die Registrierung fehlgeschlagen ist
                    this.loginError = result.message || "Registrierung fehlgeschlagen";
                }
            } catch (error) {
                // Fehlerbehandlung für den Fall, dass die Anfrage nicht ausgeführt werden kann
                console.error("Fehler bei der Registrierung:", error);
                this.loginError = "Ein Fehler ist aufgetreten.";
            }
        },

        // Funktion zum Schließen des Erfolgspopups
        closeSuccessPopup() {
            this.showSuccessPopup = false;
            this.close(); // Schließt auch das Modal, nachdem der Benutzer auf "OK" geklickt hat
        },
    }
};
</script>

<style scoped>
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
}

/* Stil für die Eingabefelder */
.form-group {
    margin-bottom: 1rem;
}

.form-group input {
    width: 100%;
    padding: 0.5rem;
    border-radius: 4px;
    border: 1px solid #ddd;
}

/* Stil für den Submit-Button */
.submit-btn {
    background-color: rgb(130, 167, 180);
    color: white;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 1rem;
}

/* Hover-Effekt für den Submit-Button */
.submit-btn:hover {
    background-color: rgb(25, 90, 130);
}

/* Stil für den Schließen-Button */
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

/* Stil für die Fehlernachricht */
.error-message {
    color: red;
    margin-top: 1rem;
    font-size: 0.9rem;
}

/* Stil für den Bestätigungs-Popup */
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