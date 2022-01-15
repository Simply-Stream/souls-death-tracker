import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        mercureUrl: String,
    };

    eventSource;

    connect() {
        this.eventSource = new EventSource(this.mercureUrlValue);
        this.eventSource.onmessage = e => this.handleMercureEvent(JSON.parse(e.data));
    }

    handleMercureEvent(mercureEvent) {
        if (mercureEvent.updated && mercureEvent.updated.id) {
            const elementToUpdate = document.getElementById(mercureEvent.updated.id);
            const deathCounter = elementToUpdate.getElementsByTagName('div')[1];

            deathCounter.innerHTML = mercureEvent.updated.deaths;
        }
    }
}
