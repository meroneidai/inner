import { JourneyWithin } from './JourneyWithin';

let instance = null;

export async function bootJourney() {
    const root = document.querySelector('[data-journey-root]');
    if (!root || instance) {
        return instance;
    }

    instance = new JourneyWithin(root);
    await instance.init();
    return instance;
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        bootJourney();
    });
} else {
    bootJourney();
}
