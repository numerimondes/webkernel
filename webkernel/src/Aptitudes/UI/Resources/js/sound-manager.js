// Sound & Notification System
window.WebsiteBuilderSoundManager = {
    sounds: {},
    permissions: {
        sound: false,
        notification: false,
        lastRequest: 0
    },

    // Initialize sounds from filesystem
    init: function() {
        this.loadSoundsFromFilesystem();
    },

    // Load sounds from filesystem
    loadSoundsFromFilesystem: function() {
        // Get the base path for sounds
        const basePath = '/webkernel/src/Aptitudes/UI/Resources/js/sounds/lib/';

        // Define available sounds with their filenames
        const soundFiles = {
            'access-allowed-tone': 'access-allowed-tone.wav',
            'notification-sent': 'message-pop-alert.mp3',
            'success': 'correct-answer.wav',
            'click': 'light-button.wav',
            'notification': 'dry-pop-up-notification-alert.wav',
            'error': 'urgent-simple-tone-loop.wav',
            'hover': 'interface-option-select.wav',
            'confirmation': 'confirmation-tone.wav',
            'doorbell': 'doorbell-tone.wav',
            'magic': 'magic-notification-ring.wav',
            'happy': 'happy-bells-notification.wav',
            'elevator': 'elevator-tone.wav',
            'telephone': 'vintage-telephone-ringtone.wav',
            'interface-start': 'software-interface-start.wav',
            'interface-back': 'software-interface-back.wav',
            'interface-remove': 'software-interface-remove.wav'
        };

        // Build sound URLs
        for (const [name, filename] of Object.entries(soundFiles)) {
            this.sounds[name] = basePath + filename;
        }
    },

    // Request permissions
    requestPermissions: function() {
        const now = Date.now();
        const twentyMinutes = 20 * 60 * 1000;

        // Don't ask again if asked less than 20 minutes ago
        if (now - this.permissions.lastRequest < twentyMinutes) {
            return;
        }

        this.permissions.lastRequest = now;

        // Request notification permission
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission().then(permission => {
                this.permissions.notification = permission === 'granted';
                console.log('Notification permission:', permission);
            });
        }

        // For sound, we'll try to play a silent sound to get permission
        this.requestSoundPermission();
    },

    // Request sound permission by playing a silent sound
    requestSoundPermission: function() {
        const silentAudio = new Audio('data:audio/wav;base64,UklGRigAAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQAAAAA=');
        silentAudio.volume = 0.01;
        silentAudio.play().then(() => {
            this.permissions.sound = true;
            console.log('Sound permission granted');
        }).catch(() => {
            this.permissions.sound = false;
            console.log('Sound permission denied');
        });
    },

    // Play a sound
    play: function(soundName, volume = 0.5) {
        if (!this.sounds[soundName]) {
            console.warn(`Sound '${soundName}' not found`);
            return;
        }

        const audio = new Audio(this.sounds[soundName]);
        audio.volume = volume;
        audio.play().catch(e => {
            console.warn('Could not play sound:', e);
            this.permissions.sound = false;
        });
    },

    // Show notification with sound
    notify: function(title, body, soundName = 'notification-sent') {
        if (this.permissions.notification && 'Notification' in window) {
            const notification = new Notification(title, {
                body: body,
                icon: '/favicon.ico'
            });

            if (this.permissions.sound) {
                this.play(soundName);
            }

            // Auto close after 3 seconds
            setTimeout(() => notification.close(), 3000);
        } else {
            // Fallback: just play sound
            if (this.permissions.sound) {
                this.play(soundName);
            }
        }
    },

    // Add a new sound
    addSound: function(name, url) {
        this.sounds[name] = url;
    },

    // Play sound with custom URL
    playCustom: function(url, volume = 0.5) {
        const audio = new Audio(url);
        audio.volume = volume;
        audio.play().catch(e => {
            console.warn('Could not play custom sound:', e);
        });
    }
};

// Global functions
window.playSound = function(soundName, volume = 0.5) {
    WebsiteBuilderSoundManager.play(soundName, volume);
};

window.showNotification = function(title, body, soundName = 'notification-sent') {
    WebsiteBuilderSoundManager.notify(title, body, soundName);
};

window.requestPermissions = function() {
    WebsiteBuilderSoundManager.requestPermissions();
};

// Auto-initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    WebsiteBuilderSoundManager.init();

    // Request permissions after a short delay
    setTimeout(() => {
        WebsiteBuilderSoundManager.requestPermissions();
    }, 1000);
});
