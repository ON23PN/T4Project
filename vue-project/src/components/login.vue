<template>
    <!-- Modal, das angezeigt wird, wenn das Login- oder Registrierungsformular sichtbar ist -->
    <div v-if="isModalVisible" class="overlay" @click.self="closeModal">
        <div class="modal-box">
            <!-- Der Titel ändert sich je nachdem, ob der Nutzer sich einloggen oder registrieren möchte -->
            <h2>{{ isRegisterMode ? 'Erstellen Sie ein Konto' : 'Melden Sie sich an' }}</h2>

            <!-- Login-Formular -->
            <form v-if="!isRegisterMode" @submit.prevent="handleLogin">
                <div class="input-group">
                    <input type="email" id="email" placeholder="E-Mail Adresse" v-model="email" required />
                </div>
                <div class="input-group">
                    <input type="password" id="password" placeholder="Passwort" v-model="password" required />
                </div>
                <button type="submit" class="submit-button">Einloggen</button>
                <p>Kein Konto? <a href="#" @click.prevent="toggleFormMode">Jetzt registrieren</a></p>
            </form>

            <!-- Registrierungsformular -->
            <form v-if="isRegisterMode" @submit.prevent="handleRegister">
                <div class="input-group">
                    <input type="text" id="firstName" placeholder="Vorname" v-model="firstName" required />
                </div>
                <div class="input-group">
                    <input type="text" id="lastName" placeholder="Nachname" v-model="lastName" required />
                </div>
                <div class="input-group">
                    <input type="email" id="email" placeholder="E-Mail Adresse" v-model="email" required />
                </div>
                <div class="input-group">
                    <input type="password" id="password" placeholder="Passwort" v-model="password" required />
                </div>
                <div class="input-group">
                    <input type="password" id="confirmPassword" placeholder="Passwort bestätigen"
                        v-model="confirmPassword" required />
                </div>
                <button type="submit" class="submit-button">Registrieren</button>
                <p>Schon ein Konto? <a href="#" @click.prevent="toggleFormMode">Einloggen</a></p>
            </form>

            <!-- Button zum Schließen des Modals -->
            <button type="button" class="close-button" @click="closeModal">Schließen</button>

            <!-- Anzeige einer Fehlermeldung, wenn beim Login oder der Registrierung ein Fehler auftritt -->
            <p v-if="errorMessage" class="error-text">{{ errorMessage }}</p>
        </div>
    </div>
</template>

<script>
export default {
    name: "AuthModal",
    data() {
        return {
            email: "", // E-Mail des Benutzers
            password: "", // Passwort des Benutzers
            confirmPassword: "", // Passwortbestätigung, nur für Registrierung
            firstName: "", // Vorname, nur für Registrierung
            lastName: "", // Nachname, nur für Registrierung
            errorMessage: "", // Fehlernachricht bei fehlerhaften Anmeldedaten
            isModalVisible: true, // Steuert, ob das Modal sichtbar ist
            isRegisterMode: false, // Wechselt zwischen Login und Registrierung
            showSuccessPopup: false, // Steuert das Anzeigen des Erfolgspopups
        };
    },
    methods: {
        // Wechselt zwischen dem Login- und dem Registrierungsmodus
        toggleFormMode() {
            this.isRegisterMode = !this.isRegisterMode;
        },

        // Logik für den Login
        async handleLogin() {
            // Formulardaten für den Login vorbereiten
            const formData = new FormData();
            formData.append("email", this.email);
            formData.append("password", this.password);

            try {
                // Anfrage an den Server senden, um den Login zu verarbeiten
                const response = await fetch('http://localhost/Projekt/vue-project/backend/login.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'include', // Sendet Cookies mit, um die Sitzung zu beibehalten
                });

                const textResponse = await response.text(); // Antwort vom Server als Text
                console.log("Antwort als Text: ", textResponse);

                // Versuch, die Antwort als JSON zu interpretieren
                let responseData;
                try {
                    responseData = JSON.parse(textResponse);
                } catch (error) {
                    console.error("Fehler beim Parsen der Antwort:", error);
                    this.errorMessage = "Fehler bei der Antwortverarbeitung.";
                    return;
                }

                // Wenn die Antwort "Login erfolgreich!" enthält, den Login bestätigen
                if (responseData.message === "Login erfolgreich!") {
                    console.log("Login war erfolgreich");
                    this.showSuccessPopup = true; // Zeigt das Erfolgspopup an
                    this.closeModal(); // Schließt das Modal nach erfolgreichem Login
                } else {
                    // Zeigt eine Fehlermeldung, wenn der Login fehlgeschlagen ist
                    this.errorMessage = responseData.message || "Login fehlgeschlagen";
                }
            } catch (error) {
                // Fehlerbehandlung für den Fall, dass die Anfrage nicht ausgeführt werden kann
                console.error("Fehler beim Login:", error);
                this.errorMessage = "Ein Problem ist aufgetreten.";
            }
        },

        // Funktion zum Schließen des Modals
        closeModal() {
            this.isModalVisible = false;
            this.$emit('close'); // Benachrichtigt den Elternkomponent, dass das Modal geschlossen werden soll
        },

        // Funktion für die Registrierung des Benutzers
        async handleRegister() {
            // Überprüft, ob die Passwörter übereinstimmen
            if (this.password !== this.confirmPassword) {
                this.errorMessage = "Die Passwörter stimmen nicht überein.";
                return;
            }

            // Formulardaten für die Registrierung sammeln
            const formData = new FormData();
            formData.append("firstName", this.firstName);
            formData.append("lastName", this.lastName);
            formData.append("email", this.email);
            formData.append("password", this.password);

            try {
                // Anfrage an den Server senden, um den Benutzer zu registrieren
                const response = await fetch('http://localhost/Projekt/vue-project/backend/register.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'include', // Sendet Cookies mit, um die Sitzung zu beibehalten
                });

                const result = await response.json();

                // Wenn die Registrierung erfolgreich war
                if (result.message === "Registrierung erfolgreich!") {
                    console.log("Registrierung war erfolgreich");
                    this.toggleFormMode(); // Wechselt zum Login-Modus
                } else {
                    // Zeigt eine Fehlermeldung an, wenn die Registrierung fehlgeschlagen ist
                    this.errorMessage = result.message || "Registrierung fehlgeschlagen";
                }
            } catch (error) {
                // Fehlerbehandlung für den Fall, dass die Anfrage nicht ausgeführt werden kann
                console.error("Fehler bei der Registrierung:", error);
                this.errorMessage = "Ein Fehler ist aufgetreten.";
            }
        },

        // Funktion zum Schließen des Erfolgspopups
        dismissSuccessPopup() {
            this.showSuccessPopup = false;
            this.closeModal(); // Schließt auch das Modal, nachdem der Benutzer auf "OK" geklickt hat
        },
    }
};
</script>

<style scoped>
.overlay {
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
    /* Stellt sicher, dass es über anderen Elementen angezeigt wird */
}

.modal-box {
    background-color: white;
    padding: 2rem;
    border-radius: 8px;
    width: 90%;
    max-width: 400px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    text-align: center;
}

/* Stil für die Eingabefelder */
.input-group {
    margin-bottom: 1rem;
}

.input-group input {
    width: 100%;
    padding: 0.5rem;
    border-radius: 4px;
    border: 1px solid #ddd;
}

/* Stil für den Submit-Button */
.submit-button {
    background-color: rgb(130, 167, 180);
    color: white;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 1rem;
}

/* Hover-Effekt für den Submit-Button */
.submit-button:hover {
    background-color: rgb(25, 90, 130);
}

/* Stil für den Schließen-Button */
.close-button {
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
.error-text {
    color: red;
    margin-top: 1rem;
    font-size: 0.9rem;
}
</style>
