<?php
    namespace App\Services;

    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Facades\Log;

    class KuantazEndpointsService {
        protected string $benefitsEndpoint;
        protected string $filtersEndpoint;
        protected string $filesEndpoint;
        
        public function __construct()
        {
            $this->benefitsEndpoint = config('services.kuantaz.benefits_endpoint');
            $this->filtersEndpoint = config('services.kuantaz.filters_endpoint');
            $this->filesEndpoint = config('services.kuantaz.files_endpoint');
        }

        public function getBenefits() {
            return $this->getData($this->benefitsEndpoint);
        }

        public function getFilters() {
            return $this->getData($this->filtersEndpoint);
        }

        public function getFiles() {
            return $this->getData($this->filesEndpoint);
        }

        private function getData($endpoint) {
            try {
                $data = Http::get($endpoint)->json();
                if ($data['code'] == 200 && $data['success']) {
                    return $data['data'];
                }
                return [];
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return [];
            }
        }
    }