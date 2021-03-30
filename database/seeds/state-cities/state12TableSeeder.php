<?php

use Illuminate\Database\Seeder;

class state12TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Create Cities for the state of HI - Hawaii.
        //If the table 'cities' exists, insert the data to the table.
        if (DB::table('cities')->get()->count() >= 0) {
            DB::table('cities')->insert([
                ['name' => 'Captain Cook', 'state_id' => 3933],
                ['name' => 'Hakalau', 'state_id' => 3933],
                ['name' => 'Hawaii National Park', 'state_id' => 3933],
                ['name' => 'Hawi', 'state_id' => 3933],
                ['name' => 'Hilo', 'state_id' => 3933],
                ['name' => 'Holualoa', 'state_id' => 3933],
                ['name' => 'Honaunau', 'state_id' => 3933],
                ['name' => 'Honokaa', 'state_id' => 3933],
                ['name' => 'Honomu', 'state_id' => 3933],
                ['name' => 'Ocean View', 'state_id' => 3933],
                ['name' => 'Waikoloa', 'state_id' => 3933],
                ['name' => 'Keauhou', 'state_id' => 3933],
                ['name' => 'Kailua Kona', 'state_id' => 3933],
                ['name' => 'Kamuela', 'state_id' => 3933],
                ['name' => 'Keaau', 'state_id' => 3933],
                ['name' => 'Kealakekua', 'state_id' => 3933],
                ['name' => 'Kapaau', 'state_id' => 3933],
                ['name' => 'Kurtistown', 'state_id' => 3933],
                ['name' => 'Laupahoehoe', 'state_id' => 3933],
                ['name' => 'Mountain View', 'state_id' => 3933],
                ['name' => 'Naalehu', 'state_id' => 3933],
                ['name' => 'Ninole', 'state_id' => 3933],
                ['name' => 'Ookala', 'state_id' => 3933],
                ['name' => 'Paauilo', 'state_id' => 3933],
                ['name' => 'Pahala', 'state_id' => 3933],
                ['name' => 'Pahoa', 'state_id' => 3933],
                ['name' => 'Papaaloa', 'state_id' => 3933],
                ['name' => 'Papaikou', 'state_id' => 3933],
                ['name' => 'Pepeekeo', 'state_id' => 3933],
                ['name' => 'Volcano', 'state_id' => 3933],
                ['name' => 'Aiea', 'state_id' => 3933],
                ['name' => 'Ewa Beach', 'state_id' => 3933],
                ['name' => 'Kapolei', 'state_id' => 3933],
                ['name' => 'Haleiwa', 'state_id' => 3933],
                ['name' => 'Hauula', 'state_id' => 3933],
                ['name' => 'Kaaawa', 'state_id' => 3933],
                ['name' => 'Kahuku', 'state_id' => 3933],
                ['name' => 'Kailua', 'state_id' => 3933],
                ['name' => 'Kaneohe', 'state_id' => 3933],
                ['name' => 'Kunia', 'state_id' => 3933],
                ['name' => 'Laie', 'state_id' => 3933],
                ['name' => 'Pearl City', 'state_id' => 3933],
                ['name' => 'Wahiawa', 'state_id' => 3933],
                ['name' => 'Mililani', 'state_id' => 3933],
                ['name' => 'Waialua', 'state_id' => 3933],
                ['name' => 'Waianae', 'state_id' => 3933],
                ['name' => 'Waimanalo', 'state_id' => 3933],
                ['name' => 'Waipahu', 'state_id' => 3933],
                ['name' => 'Honolulu', 'state_id' => 3933],
                ['name' => 'Jbphh', 'state_id' => 3933],
                ['name' => 'Wheeler Army Airfield', 'state_id' => 3933],
                ['name' => 'Schofield Barracks', 'state_id' => 3933],
                ['name' => 'Fort Shafter', 'state_id' => 3933],
                ['name' => 'Tripler Army Medical Center', 'state_id' => 3933],
                ['name' => 'Camp H M Smith', 'state_id' => 3933],
                ['name' => 'Mcbh Kaneohe Bay', 'state_id' => 3933],
                ['name' => 'Wake Island', 'state_id' => 3933],
                ['name' => 'Anahola', 'state_id' => 3933],
                ['name' => 'Eleele', 'state_id' => 3933],
                ['name' => 'Hanalei', 'state_id' => 3933],
                ['name' => 'Hanamaulu', 'state_id' => 3933],
                ['name' => 'Hanapepe', 'state_id' => 3933],
                ['name' => 'Princeville', 'state_id' => 3933],
                ['name' => 'Kalaheo', 'state_id' => 3933],
                ['name' => 'Kapaa', 'state_id' => 3933],
                ['name' => 'Kaumakani', 'state_id' => 3933],
                ['name' => 'Kealia', 'state_id' => 3933],
                ['name' => 'Kekaha', 'state_id' => 3933],
                ['name' => 'Kilauea', 'state_id' => 3933],
                ['name' => 'Koloa', 'state_id' => 3933],
                ['name' => 'Lawai', 'state_id' => 3933],
                ['name' => 'Lihue', 'state_id' => 3933],
                ['name' => 'Makaweli', 'state_id' => 3933],
                ['name' => 'Waimea', 'state_id' => 3933],
                ['name' => 'Haiku', 'state_id' => 3933],
                ['name' => 'Hana', 'state_id' => 3933],
                ['name' => 'Hoolehua', 'state_id' => 3933],
                ['name' => 'Kahului', 'state_id' => 3933],
                ['name' => 'Kalaupapa', 'state_id' => 3933],
                ['name' => 'Kaunakakai', 'state_id' => 3933],
                ['name' => 'Kihei', 'state_id' => 3933],
                ['name' => 'Kualapuu', 'state_id' => 3933],
                ['name' => 'Lahaina', 'state_id' => 3933],
                ['name' => 'Lanai City', 'state_id' => 3933],
                ['name' => 'Makawao', 'state_id' => 3933],
                ['name' => 'Maunaloa', 'state_id' => 3933],
                ['name' => 'Paia', 'state_id' => 3933],
                ['name' => 'Puunene', 'state_id' => 3933],
                ['name' => 'Pukalani', 'state_id' => 3933],
                ['name' => 'Kula', 'state_id' => 3933],
                ['name' => 'Wailuku', 'state_id' => 3933]
            ]);
        }
    }
}
