document.addEventListener("DOMContentLoaded", async () => {
    const form = document.getElementById("edit-profile-form");
    const submitBtn = document.getElementById("submit-btn");

    const fields = {
        full_name: document.getElementById("full_name"),
        pseudo: document.getElementById("pseudo"),
        email: document.getElementById("email"),
        iban: document.getElementById("iban")
    };

    const errors = {
        full_name: document.getElementById("full_name_error"),
        pseudo: document.getElementById("pseudo_error"),
        email: document.getElementById("email_error"),
        iban: document.getElementById("iban_error")
    };

    const touched = {
        full_name: false,
        pseudo: false,
        email: false,
        iban: false
    };

    const config = await fetch("user/get_validation_config_service").then(r => r.json());

    function setError(name, message) {
        const input = fields[name];
        const errorDiv = errors[name];

        errorDiv.textContent = message;

        if (!touched[name]) {
            input.classList.remove("input-error", "input-valid");
            return;
        }

        if (message !== "") {
            input.classList.add("input-error");
            input.classList.remove("input-valid");
        } else {
            input.classList.remove("input-error");
            input.classList.add("input-valid");
        }
    }

    function clearField(name) {
        errors[name].textContent = "";
        fields[name].classList.remove("input-error", "input-valid");
    }

    function updateSubmit() {
        const hasErrors = Object.values(errors).some(div => div.textContent !== "");
        submitBtn.disabled = hasErrors;
    }

    function getSyncError(name) {
        const value = fields[name].value.trim();

        if (name === "full_name") {
            if (value === "") return "Full name is required.";
            if (value.length < config.varchar_min_length) {
                return `Full name must be at least ${config.varchar_min_length} characters.`;
            }
            return "";
        }

        if (name === "pseudo") {
            if (value === "") return "Pseudo is required.";
            if (value.length < config.username_min_length || value.length > config.username_max_length) {
                return `Pseudo must be between ${config.username_min_length} and ${config.username_max_length} characters.`;
            }
            return "";
        }

        if (name === "email") {
            if (value === "") return "Email is required.";
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                return "Must be a valid email address.";
            }
            return "";
        }

        if (name === "iban") {
            if (value !== "" && !/^BE\d{14}$/.test(value.replace(/\s/g, "").toUpperCase())) {
                return "IBAN must be of the form BE99 9999 9999 9999.";
            }
            return "";
        }

        return "";
    }

async function getAsyncError(name) {
    const value = fields[name].value.trim();
    let url = "";

    if (name === "full_name") {
        url = "user/check_full_name_service";
    } else if (name === "pseudo") {
        url = "user/check_pseudo_service";
    } else if (name === "email") {
        url = "user/check_email_service";
    } else {
        return "";
    }

    const currentValue = value;

    try {
        let data = await $.post(url, { param1: value }, null, "json");

        if (fields[name].value.trim() !== currentValue) {
            return null;
        }

        if (data.ok) {
            return "";
        }

        if (data.errors && data.errors.length > 0) {
            return data.errors[0];
        }

        return "";
    } catch (e) {
        if (fields[name].value.trim() !== currentValue) {
            return null;
        }

        let data = null;

        if (e.responseJSON) {
            data = e.responseJSON;
        } else if (e.responseText) {
            try {
                data = JSON.parse(e.responseText);
            } catch (e) {
                data = null;
            }
        }

        if (data && data.errors && data.errors.length > 0) {
            return data.errors[0];
        }

        return "";
    }
}
    async function validateField(name) {
        if (!touched[name]) {
            clearField(name);
            updateSubmit();
            return;
        }

        const syncError = getSyncError(name);

        if (syncError !== "") {
            setError(name, syncError);
            updateSubmit();
            return;
        }

        setError(name, "");
        updateSubmit();

        if (name === "full_name" || name === "pseudo" || name === "email") {
            const asyncError = await getAsyncError(name);

            if (asyncError === null) {
                return;
            }

            setError(name, asyncError);
            updateSubmit();
        }
    }

    for (const name in fields) {
        fields[name].addEventListener("input", async () => {
            touched[name] = true;
            await validateField(name);
        });

        fields[name].addEventListener("blur", async () => {
            touched[name] = true;
            await validateField(name);
        });
    }

    form.addEventListener("submit", async (e) => {
        for (const name in touched) {
            touched[name] = true;
            await validateField(name);
        }

        updateSubmit();

        if (submitBtn.disabled) {
            e.preventDefault();
        }
    });
});