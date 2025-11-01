// WebkernelBuilder Sound & Notification System
window.WebkernelBuilderSoundManager = {
    sounds: {
        'notification-sent': 'https://assets.mixkit.co/active_storage/sfx/2358/2358-preview.mp3',
        'success': 'https://www.soundjay.com/misc/sounds/success.mp3',
        'click': 'https://www.soundjay.com/misc/sounds/click.mp3',
        'notification': 'https://www.soundjay.com/misc/sounds/notification.mp3',
        'error': 'https://www.soundjay.com/misc/sounds/error.mp3',
        'hover': 'https://www.soundjay.com/misc/sounds/hover.mp3'
    },

    permissions: {
        sound: false,
        notification: false,
        lastRequest: 0
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
                console.log('WebkernelBuilder notification permission:', permission);
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
            console.log('WebkernelBuilder sound permission granted');
        }).catch(() => {
            this.permissions.sound = false;
            console.log('WebkernelBuilder sound permission denied');
        });
    },

    // Play a sound
    play: function(soundName, volume = 0.5) {
        if (!this.sounds[soundName]) {
            console.warn(`WebkernelBuilder sound '${soundName}' not found`);
            return;
        }

        const audio = new Audio(this.sounds[soundName]);
        audio.volume = volume;
        audio.play().catch(e => {
            console.warn('WebkernelBuilder could not play sound:', e);
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
            console.warn('WebkernelBuilder could not play custom sound:', e);
        });
    }
};

// Global functions with WebkernelBuilder prefix
window.wkbPlaySound = function(soundName, volume = 0.5) {
    WebkernelBuilderSoundManager.play(soundName, volume);
};

window.wkbShowNotification = function(title, body, soundName = 'notification-sent') {
    WebkernelBuilderSoundManager.notify(title, body, soundName);
};

window.wkbRequestPermissions = function() {
    WebkernelBuilderSoundManager.requestPermissions();
};

// Auto-initialize permissions on page load
document.addEventListener('DOMContentLoaded', function() {
    // Request permissions after a short delay
    setTimeout(() => {
        WebkernelBuilderSoundManager.requestPermissions();
    }, 1000);
});

// Example usage:
// wkbPlaySound('notification-sent');
// wkbShowNotification('Success!', 'Your action was completed successfully');
// wkbRequestPermissions();
