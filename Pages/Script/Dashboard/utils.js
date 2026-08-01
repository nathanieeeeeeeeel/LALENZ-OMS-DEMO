function copyToClipboard(text, message, button) {
    const copyText = String(text || "").trim();
    if (!copyText) {
        toastr.error("Nothing to copy.");
        return;
    }

    function showCopySuccess() {
        const $btn = $(button);
        button.innerHTML = '<i class="fa-solid fa-check"></i> Copied!';
        button.disabled = true;
        setTimeout(() => {
        button.innerHTML = '<i class="fa-solid fa-copy"></i> Copy Address';
        button.disabled = false;
        }, 1200);
        toastr.success(message || "Text copied to clipboard!");
    }

    function fallbackCopy() {
        const textarea = document.createElement("textarea");
        textarea.value = copyText;
        textarea.setAttribute("readonly", "readonly");
        textarea.style.position = "absolute";
        textarea.style.left = "-9999px";
        document.body.appendChild(textarea);
        textarea.select();

        try {
        const successful = document.execCommand("copy");
        if (successful) {
            showCopySuccess();
        } else {
            throw new Error("execCommand copy failed");
        }
        } catch (err) {
        toastr.error("Failed to copy text.");
        console.error("Clipboard fallback error:", err);
        }

        document.body.removeChild(textarea);
    }

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(copyText).then(
        () => showCopySuccess(),
        (err) => {
            console.warn("Clipboard API failed, using fallback", err);
            fallbackCopy();
        },
        );
    } else {
        fallbackCopy();
    }
}

// Toggles the filters panel visibility.
// Used by the filter buttons in the mobile layout beside the search bar.

function toggleFilters() {
const panel = document.getElementById("filtersPanel");
const buttons = [document.getElementById("filterToggleBtn"), document.getElementById("filterToggleBtnMobile")].filter(Boolean);
if (!panel) return;
panel.classList.toggle("hidden");
const isOpen = !panel.classList.contains("hidden");
buttons.forEach((btn) => btn.setAttribute("aria-expanded", isOpen ? "true" : "false"));
if (isOpen) {
    panel.scrollIntoView({ behavior: "smooth", block: "start" });
}
}

// In dashboard.php

function handleEditPaymentChange() {
const method = document.getElementById("editPaymentMethod").value;
const otherInput = document.getElementById("editOtherPayment");

if (method === "Others") {
    otherInput.classList.remove("hidden");
} else {
    otherInput.classList.add("hidden");
    otherInput.value = "";
}
}