// This file is in Views/
// All fetch() calls must be relative to the root index.php that loaded it.

let pollInterval;
let isSeniorAvailable = false;

// --- Helper Functions ---
const $ = (selector) => document.querySelector(selector);

// Updated Show/Hide to be more reliable
const show = (selector) => {
    const el = $(selector);
    if (el) {
        el.classList.remove('hidden');
        if (selector === '#student-searching-view') {
            el.style.display = 'block';
        } else if (selector === '#senior-waiting-view') {
            el.style.display = 'flex';
        } else {
            el.style.display = 'flex';
        }
    }
};
const hide = (selector) => {
    const el = $(selector);
    if (el) {
        el.style.display = 'none';
        el.classList.add('hidden');
    }
};


// --- Student Page Logic ---
function initStudentPage() {
    const btn = $('#request-chat-btn');
    if (btn) btn.addEventListener('click', requestChat);
}

async function requestChat() {
    console.log("Student requested chat...");
    show('#student-searching-view');
    hide('#student-idle-view');

    try {
        // === FIX ===
        await fetch('index.php?view=api&action=request_chat'); // Path updated
        // ===========
        pollInterval = setInterval(pollStatus, 3000);
    } catch (e) {
        console.error("Error requesting chat:", e);
        alert("Error: Could not contact the server. Please try again.");
        hide('#student-searching-view');
        show('#student-idle-view');
    }
}

// --- Senior Page Logic ---
function initSeniorPage() {
    const availBtn = $('#senior-availability-btn');
    const cancelBtn = $('#senior-cancel-btn');

    if(availBtn) availBtn.addEventListener('click', () => toggleSeniorAvailability(true));
    if(cancelBtn) cancelBtn.addEventListener('click', () => toggleSeniorAvailability(false));
}

async function toggleSeniorAvailability(available) {
    isSeniorAvailable = available;
    console.log("Senior availability set to:", isSeniorAvailable);

    if (isSeniorAvailable) {
        hide('#senior-idle-view');
        show('#senior-waiting-view');

        try {
            // === FIX ===
            await fetch(`index.php?view=api&action=set_senior_state&available=true`); // Path updated
            // ===========
            pollInterval = setInterval(pollStatus, 3000);
        } catch (e) {
            console.error("Error setting availability:", e);
            alert("Error: Could not contact the server. Please try again.");
            show('#senior-idle-view');
            hide('#senior-waiting-view');
        }

    } else {
        show('#senior-idle-view');
        hide('#senior-waiting-view');
        clearInterval(pollInterval);

        try {
            // === FIX ===
            await fetch(`index.php?view=api&action=set_senior_state&available=false`); // Path updated
            // ===========
        } catch (e) {
            console.error("Error setting availability:", e);
        }
    }
}

// --- Polling Logic (Used by both) ---
async function pollStatus() {
    console.log("Polling for status update...");
    try {
        // === FIX ===
        const response = await fetch('index.php?view=api&action=check_status'); // Path updated
        // ===========
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();

        switch(data.status) {
            case 'start_chat':
                console.log("Match found! Starting chat.");
                clearInterval(pollInterval);
                const userType = $('#page-student-dashboard') ? 'student' : 'senior';
                window.location.href = `index.php?view=chat&user=${userType}`;
                break;
            case 'searching':
                console.log("Agent is searching...");
                break;
            case 'idle':
                console.log("Agent is idle.");
                break;
        }
    } catch (e) {
        console.error("Error polling:", e);
        clearInterval(pollInterval);
        console.log("Server returned invalid data. Stopping poll. Check API paths and file permissions.");
    }
}