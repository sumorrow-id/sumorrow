export class GearModal {
    /**
     * Initializes the GearModal class.
     * @param {HTMLElement} modal
     * @param {HTMLFormElement} form
     * @param {NodeListOf<HTMLElement>} openTriggers
     * @param {NodeListOf<HTMLElement>} closeTriggers
     */
    constructor(modal, form, openTriggers, closeTriggers) {
        if (!modal || !form) return;

        this.modal = modal;
        this.form = form;
        this.openTriggers = openTriggers;
        this.closeTriggers = closeTriggers;
        this.methodInput = document.getElementById('gear-method');
        this.titleEl = document.getElementById('gear-modal-title');
        this.nameInput = document.getElementById('gear-name');
        this.brandInput = document.getElementById('gear-brand');
        this.weightInput = document.getElementById('gear-weight');
        this.categorySelect = document.getElementById('gear-category');

        this.init();
    }

    init() {
        this.openTriggers.forEach((trigger) => trigger.addEventListener('click', () => this.openGearModal()));
        this.closeTriggers.forEach((trigger) => trigger.addEventListener('click', () => this.closeGearModal()));

        // Delegated listener for the category filter pills
        document.addEventListener('click', (event) => {
            const filterBtn = event.target.closest('[data-gear-filter]');
            if (filterBtn) {
                this.filterGear(filterBtn.dataset.gearFilter);
            }
        });

        // Delegated listener for per-item edit buttons
        document.addEventListener('click', (event) => {
            const editBtn = event.target.closest('[data-gear-id]');
            if (editBtn) {
                this.editGear(editBtn.dataset);
            }
        });

        // Trigger first category filter on load if available
        if (document.getElementById('gear-btn-all')) {
            this.filterGear('all');
        }

        // Auto-show the modal when the form was re-rendered with validation errors
        if (this.modal.dataset.hasErrors === 'true') {
            this.modal.classList.remove('hidden');
        }
    }

    filterGear(categorySlug) {
        // Reset all buttons
        document.querySelectorAll('.gear-cat-btn').forEach((btn) => {
            btn.classList.remove('bg-white', 'text-[#8C4F34]', 'font-bold', 'shadow-sm');
            btn.classList.add('text-[#6B7280]', 'font-medium');
        });

        // Set active button
        let activeBtn = document.getElementById('gear-btn-' + categorySlug);
        if (activeBtn) {
            activeBtn.classList.add('bg-white', 'text-[#8C4F34]', 'font-bold', 'shadow-sm');
            activeBtn.classList.remove('text-[#6B7280]', 'font-medium');
        }

        // Filter items
        let count = 0;
        document.querySelectorAll('.gear-item').forEach((item) => {
            if (categorySlug === 'all' || item.dataset.category === categorySlug) {
                item.style.display = 'flex';
                count++;
            } else {
                item.style.display = 'none';
            }
        });

        // Update Title & Count
        let catBtn = document.querySelector('#gear-btn-' + categorySlug);
        let catName = catBtn ? catBtn.innerText.trim() : 'All Gear';
        document.getElementById('gear-category-title').innerHTML = `${catName} <span class="text-[13px] font-normal text-[#94A3B8] ml-1" id="gear-category-count">(${count} items)</span>`;
    }

    openGearModal() {
        this.modal.classList.remove('hidden');
        this.form.action = this.form.dataset.storeUrl;
        this.methodInput.value = 'POST';
        this.titleEl.innerText = 'Add New Gear';
        this.nameInput.value = '';
        this.brandInput.value = '';
        this.weightInput.value = '';
        this.categorySelect.value = 'Backpack';
    }

    closeGearModal() {
        this.modal.classList.add('hidden');
    }

    editGear(dataset) {
        const { gearId, gearName, gearBrand, gearWeight, gearCategory } = dataset;

        this.modal.classList.remove('hidden');
        this.form.action = '/gears/' + gearId;
        this.methodInput.value = 'PUT';
        this.titleEl.innerText = 'Edit Gear';
        this.nameInput.value = gearName;
        this.brandInput.value = gearBrand;
        this.weightInput.value = gearWeight;

        let options = Array.from(this.categorySelect.options);
        if (!options.some((opt) => opt.value === gearCategory)) {
            let newOption = new Option(gearCategory, gearCategory);
            this.categorySelect.add(newOption);
        }
        this.categorySelect.value = gearCategory;
    }
}
