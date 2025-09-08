<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'registration_number' => strtoupper($this->faker->bothify('REG-#####')),
            'email' => $this->faker->unique()->companyEmail(),
            'phone' => $this->faker->e164PhoneNumber(),
            'website' => $this->faker->domainName(),
            'address' => $this->faker->address(),
        ];
    }
}
