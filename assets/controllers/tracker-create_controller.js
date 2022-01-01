import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'counter'
    ];

    form = {
        'sections': [
            {
                'name': 'Maingame',
                'counters': []
            }
        ]
    };

    add(event) {
        event.preventDefault();

        this.counterTarget.innerHTML += `
            <div class="mb-3">
                <label for="trackerCounterCauseInput">Cause</label>
                <input name="counterCause[]" type="text" class="form-control" id="trackerCounterCauseInput" placeholder="mobs">
            </div>
        `;
    }

    create(event) {
        event.preventDefault();
        // event.target.elements['counterCause[]'].each(el => {
        //     console.log(el);
        // });
        console.log(event.target.elements);
    }
}
