import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'sections', 'section',
    ];

    static values = {
        sectionsPrototype: String,
    };

    connect() {
        this.sectionsIndex = this.sectionsCountValue = this.sectionTargets.length;
    }

    addSection(event) {
        event.preventDefault();

        let prototype = JSON.parse(this.sectionsPrototypeValue);
        const newField = prototype.replace(/__name__/g, this.sectionsIndex);
        this.sectionsIndex++;
        this.sectionsCountValue++;

        this.sectionsTarget.insertAdjacentHTML('beforeend', newField);
    }

    removeSection(event) {
        event.preventDefault();

        this.sectionTargets.forEach(element => {
            if (element.contains(event.target)) {
                element.remove();
                this.sectionsCountValue--;
            }
        });
    }
}
