import { Controller } from '@hotwired/stimulus';

// @TODO: Some day, this needs to be replaced by a nice generic controller for dynamic forms
export default class extends Controller {
    static targets = [
        'causes', 'cause',
    ];

    static values = {
        causesPrototype: String,
    };

    connect() {
        this.causesIndex = this.causesCountValue = this.causeTargets.length;
    }

    addCause(event) {
        event.preventDefault();

        let prototype = JSON.parse(this.causesPrototypeValue);
        const newField = prototype.replace(/__NAME__/g, this.causesIndex);
        this.causesIndex++;
        this.causesCountValue++;

        this.causesTarget.insertAdjacentHTML('beforeend', newField);
    }

    removeCause(event) {
        event.preventDefault();

        this.causeTargets.forEach(element => {
            if (element.contains(event.target)) {
                element.remove();
                this.causesCountValue--;
            }
        });
    }
}
