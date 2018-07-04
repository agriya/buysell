<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('UserTableSeeder');
		$this->command->info('User table seeded!');

		$this->call('ProductCategoryTableSeeder');
		$this->command->info('Categories seeded!');

		$this->call('ProductTableSeeder');
		$this->command->info('Products seeded!');

		$this->call('GeneralTableSeeder');
		$this->command->info('General Table seeded!');

		$this->call('DealTableSeeder');
		$this->command->info('Deal Table seeded!');

	}

}