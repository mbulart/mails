<?php

namespace Database\Seeders;

use App\Models\ApiConsumer;
use Illuminate\Database\Seeder;

class ApiConsumerSeeder extends Seeder
{
    public function run(): void
    {
        if (ApiConsumer::query()->where('email', 'dev@example.com')->exists()) {
            return;
        }

        $key = ApiConsumer::makeApiKey();

        ApiConsumer::query()->create([
            'name' => 'Consommateur developpement',
            'email' => 'dev@example.com',
            'api_key_hash' => $key['hash'],
            'api_key_preview' => $key['preview'],
            'api_key_plain' => $key['plain'],
            'is_active' => true,
            'rate_limit' => 100,
        ]);
    }
}
<?php

namespace Database\Seeders;

use App\Models\ApiConsumer;
use Illuminate\Database\Seeder;

class ApiConsumerSeeder extends Seeder
{
    public function run(): void
    {
        if (ApiConsumer::query()->where('email', 'dev@example.com')->exists()) {
            return;
        }

        $key = ApiConsumer::makeApiKey();

        ApiConsumer::query()->create([
            'name' => 'Consommateur développement',
            'email' => 'dev@example.com',
            'api_key_hash' => $key['hash'],
            'api_key_preview' => $key['preview'],
            'api_key_plain' => $key['plain'],
            'is_active' => true,
            'rate_limit' => 100,
        ]);
    }
}
