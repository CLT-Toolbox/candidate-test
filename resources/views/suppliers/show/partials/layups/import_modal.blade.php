<div x-data="{
    step: 'upload', {{-- 'upload' or 'conflicts' --}}
    file: null,
    fileName: '',
    strategy: 'skip',
    dryRun: false,
    analysis: null,
    loading: false,
    rawData: null,
    resolutions: {},

    handleFileSelect(e) {
        const file = e.target.files[0];
        if (!file) return;
        this.file = file;
        this.fileName = file.name;
        this.analyze();
    },

    analyze() {
        if (!this.file) return;
        this.loading = true;

        const formData = new FormData();
        formData.append('file', this.file);

        axios.post('{{ route('suppliers.import.analyze', $supplier->id) }}', formData)
            .then(response => {
                this.analysis = response.data.data.analysis;
                this.rawData = response.data.data.raw_data;
                this.resolutions = {};
                
                // Initialize resolutions based on current strategy
                this.analysis.conflicts.forEach(c => {
                    this.resolutions[c.name] = this.strategy;
                });
            })
            .catch(error => {
                alert('Error analyzing file: ' + (error.response?.data?.message || error.message));
                this.reset();
            })
            .finally(() => {
                this.loading = false;
                this.$nextTick(() => typeof createIcons === 'function' && createIcons());
            });
    },

    applyToAll(type) {
        if (!this.analysis) return;
        this.strategy = type;
        this.analysis.conflicts.forEach(c => {
            this.resolutions[c.name] = type;
        });
    },

    confirm() {
        if (!this.file || this.loading) return;
        
        this.loading = true;
        axios.post('{{ route('suppliers.import.confirm', $supplier->id) }}', {
            data: this.rawData,
            strategy: this.strategy,
            resolutions: this.resolutions,
            dry_run: this.dryRun
        })
        .then(response => {
            if (this.dryRun) {
                alert('Dry run completed successfully. No changes were saved.');
                this.loading = false;
            } else {
                window.location.reload();
            }
        })
        .catch(error => {
            this.loading = false;
            alert('Import failed: ' + (error.response?.data?.message || error.message));
        });
    },

    reset() {
        this.file = null;
        this.fileName = '';
        this.analysis = null;
        this.step = 'upload';
    }
}" x-init="$watch('step', () => $nextTick(() => typeof createIcons === 'function' && createIcons()))">
    <x-ui.modal name="import-layups" maxWidth="2xl" title="Import Layup Data">
        <div class="px-2"> {{-- Subtle interior margin, modal component already handles major padding --}}
            {{-- Step Content --}}
            <div x-show="step === 'upload'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                @include('suppliers.show.partials.layups.import.step_upload')
            </div>

            <div x-show="step === 'conflicts'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                @include('suppliers.show.partials.layups.import.step_conflicts')
            </div>
        </div>
    </x-ui.modal>
</div>

