import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'total'
    ];

    static values = {
        mercureUrl: String,
    };

    eventSource;

    connect() {
        this.eventSource = new EventSource(this.mercureUrlValue);
        this.eventSource.onmessage = e => this.handleMercureEvent(JSON.parse(e.data));
    }

    handleMercureEvent(mercureEvent) {
        if (mercureEvent.total) {
            this.totalTarget.innerHTML = mercureEvent.total;
        }
    }
}
