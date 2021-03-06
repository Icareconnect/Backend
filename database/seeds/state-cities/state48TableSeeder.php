<?php

use Illuminate\Database\Seeder;

class state48TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Create Cities for the state of WA - Washington.
        //If the table 'cities' exists, insert the data to the table.
        if (DB::table('cities')->get()->count() >= 0) {
            DB::table('cities')->insert([
                ['name' => 'Benge', 'state_id' => 3976],
                ['name' => 'Ritzville', 'state_id' => 3976],
                ['name' => 'Lind', 'state_id' => 3976],
                ['name' => 'Othello', 'state_id' => 3976],
                ['name' => 'Washtucna', 'state_id' => 3976],
                ['name' => 'Anatone', 'state_id' => 3976],
                ['name' => 'Asotin', 'state_id' => 3976],
                ['name' => 'Clarkston', 'state_id' => 3976],
                ['name' => 'Benton City', 'state_id' => 3976],
                ['name' => 'Kennewick', 'state_id' => 3976],
                ['name' => 'Paterson', 'state_id' => 3976],
                ['name' => 'Plymouth', 'state_id' => 3976],
                ['name' => 'Prosser', 'state_id' => 3976],
                ['name' => 'Richland', 'state_id' => 3976],
                ['name' => 'West Richland', 'state_id' => 3976],
                ['name' => 'Wenatchee', 'state_id' => 3976],
                ['name' => 'Ardenvoir', 'state_id' => 3976],
                ['name' => 'Cashmere', 'state_id' => 3976],
                ['name' => 'Chelan', 'state_id' => 3976],
                ['name' => 'Chelan Falls', 'state_id' => 3976],
                ['name' => 'Dryden', 'state_id' => 3976],
                ['name' => 'Entiat', 'state_id' => 3976],
                ['name' => 'Leavenworth', 'state_id' => 3976],
                ['name' => 'Malaga', 'state_id' => 3976],
                ['name' => 'Manson', 'state_id' => 3976],
                ['name' => 'Monitor', 'state_id' => 3976],
                ['name' => 'Peshastin', 'state_id' => 3976],
                ['name' => 'Stehekin', 'state_id' => 3976],
                ['name' => 'Beaver', 'state_id' => 3976],
                ['name' => 'Carlsborg', 'state_id' => 3976],
                ['name' => 'Clallam Bay', 'state_id' => 3976],
                ['name' => 'Forks', 'state_id' => 3976],
                ['name' => 'Joyce', 'state_id' => 3976],
                ['name' => 'La Push', 'state_id' => 3976],
                ['name' => 'Neah Bay', 'state_id' => 3976],
                ['name' => 'Port Angeles', 'state_id' => 3976],
                ['name' => 'Sekiu', 'state_id' => 3976],
                ['name' => 'Sequim', 'state_id' => 3976],
                ['name' => 'Amboy', 'state_id' => 3976],
                ['name' => 'Battle Ground', 'state_id' => 3976],
                ['name' => 'Brush Prairie', 'state_id' => 3976],
                ['name' => 'Camas', 'state_id' => 3976],
                ['name' => 'Heisson', 'state_id' => 3976],
                ['name' => 'La Center', 'state_id' => 3976],
                ['name' => 'Ridgefield', 'state_id' => 3976],
                ['name' => 'Vancouver', 'state_id' => 3976],
                ['name' => 'Washougal', 'state_id' => 3976],
                ['name' => 'Yacolt', 'state_id' => 3976],
                ['name' => 'Dayton', 'state_id' => 3976],
                ['name' => 'Starbuck', 'state_id' => 3976],
                ['name' => 'Ryderwood', 'state_id' => 3976],
                ['name' => 'Ariel', 'state_id' => 3976],
                ['name' => 'Carrolls', 'state_id' => 3976],
                ['name' => 'Castle Rock', 'state_id' => 3976],
                ['name' => 'Cougar', 'state_id' => 3976],
                ['name' => 'Kalama', 'state_id' => 3976],
                ['name' => 'Kelso', 'state_id' => 3976],
                ['name' => 'Longview', 'state_id' => 3976],
                ['name' => 'Silverlake', 'state_id' => 3976],
                ['name' => 'Toutle', 'state_id' => 3976],
                ['name' => 'Woodland', 'state_id' => 3976],
                ['name' => 'East Wenatchee', 'state_id' => 3976],
                ['name' => 'Bridgeport', 'state_id' => 3976],
                ['name' => 'Mansfield', 'state_id' => 3976],
                ['name' => 'Orondo', 'state_id' => 3976],
                ['name' => 'Palisades', 'state_id' => 3976],
                ['name' => 'Rock Island', 'state_id' => 3976],
                ['name' => 'Waterville', 'state_id' => 3976],
                ['name' => 'Boyds', 'state_id' => 3976],
                ['name' => 'Curlew', 'state_id' => 3976],
                ['name' => 'Danville', 'state_id' => 3976],
                ['name' => 'Inchelium', 'state_id' => 3976],
                ['name' => 'Keller', 'state_id' => 3976],
                ['name' => 'Laurier', 'state_id' => 3976],
                ['name' => 'Malo', 'state_id' => 3976],
                ['name' => 'Orient', 'state_id' => 3976],
                ['name' => 'Republic', 'state_id' => 3976],
                ['name' => 'Pasco', 'state_id' => 3976],
                ['name' => 'Connell', 'state_id' => 3976],
                ['name' => 'Eltopia', 'state_id' => 3976],
                ['name' => 'Kahlotus', 'state_id' => 3976],
                ['name' => 'Mesa', 'state_id' => 3976],
                ['name' => 'Pomeroy', 'state_id' => 3976],
                ['name' => 'Ephrata', 'state_id' => 3976],
                ['name' => 'George', 'state_id' => 3976],
                ['name' => 'Marlin', 'state_id' => 3976],
                ['name' => 'Moses Lake', 'state_id' => 3976],
                ['name' => 'Quincy', 'state_id' => 3976],
                ['name' => 'Soap Lake', 'state_id' => 3976],
                ['name' => 'Stratford', 'state_id' => 3976],
                ['name' => 'Warden', 'state_id' => 3976],
                ['name' => 'Wilson Creek', 'state_id' => 3976],
                ['name' => 'Coulee City', 'state_id' => 3976],
                ['name' => 'Electric City', 'state_id' => 3976],
                ['name' => 'Grand Coulee', 'state_id' => 3976],
                ['name' => 'Hartline', 'state_id' => 3976],
                ['name' => 'Beverly', 'state_id' => 3976],
                ['name' => 'Mattawa', 'state_id' => 3976],
                ['name' => 'Royal City', 'state_id' => 3976],
                ['name' => 'Aberdeen', 'state_id' => 3976],
                ['name' => 'Amanda Park', 'state_id' => 3976],
                ['name' => 'Copalis Beach', 'state_id' => 3976],
                ['name' => 'Copalis Crossing', 'state_id' => 3976],
                ['name' => 'Cosmopolis', 'state_id' => 3976],
                ['name' => 'Elma', 'state_id' => 3976],
                ['name' => 'Grayland', 'state_id' => 3976],
                ['name' => 'Hoquiam', 'state_id' => 3976],
                ['name' => 'Humptulips', 'state_id' => 3976],
                ['name' => 'Mccleary', 'state_id' => 3976],
                ['name' => 'Malone', 'state_id' => 3976],
                ['name' => 'Moclips', 'state_id' => 3976],
                ['name' => 'Montesano', 'state_id' => 3976],
                ['name' => 'Neilton', 'state_id' => 3976],
                ['name' => 'Oakville', 'state_id' => 3976],
                ['name' => 'Ocean Shores', 'state_id' => 3976],
                ['name' => 'Pacific Beach', 'state_id' => 3976],
                ['name' => 'Quinault', 'state_id' => 3976],
                ['name' => 'Satsop', 'state_id' => 3976],
                ['name' => 'Taholah', 'state_id' => 3976],
                ['name' => 'Westport', 'state_id' => 3976],
                ['name' => 'Clinton', 'state_id' => 3976],
                ['name' => 'Coupeville', 'state_id' => 3976],
                ['name' => 'Freeland', 'state_id' => 3976],
                ['name' => 'Greenbank', 'state_id' => 3976],
                ['name' => 'Langley', 'state_id' => 3976],
                ['name' => 'Oak Harbor', 'state_id' => 3976],
                ['name' => 'Brinnon', 'state_id' => 3976],
                ['name' => 'Chimacum', 'state_id' => 3976],
                ['name' => 'Port Hadlock', 'state_id' => 3976],
                ['name' => 'Nordland', 'state_id' => 3976],
                ['name' => 'Port Ludlow', 'state_id' => 3976],
                ['name' => 'Port Townsend', 'state_id' => 3976],
                ['name' => 'Quilcene', 'state_id' => 3976],
                ['name' => 'Auburn', 'state_id' => 3976],
                ['name' => 'Federal Way', 'state_id' => 3976],
                ['name' => 'Bellevue', 'state_id' => 3976],
                ['name' => 'Black Diamond', 'state_id' => 3976],
                ['name' => 'Bothell', 'state_id' => 3976],
                ['name' => 'Burton', 'state_id' => 3976],
                ['name' => 'Carnation', 'state_id' => 3976],
                ['name' => 'Duvall', 'state_id' => 3976],
                ['name' => 'Enumclaw', 'state_id' => 3976],
                ['name' => 'Fall City', 'state_id' => 3976],
                ['name' => 'Hobart', 'state_id' => 3976],
                ['name' => 'Issaquah', 'state_id' => 3976],
                ['name' => 'Kenmore', 'state_id' => 3976],
                ['name' => 'Kent', 'state_id' => 3976],
                ['name' => 'Kirkland', 'state_id' => 3976],
                ['name' => 'Maple Valley', 'state_id' => 3976],
                ['name' => 'Medina', 'state_id' => 3976],
                ['name' => 'Mercer Island', 'state_id' => 3976],
                ['name' => 'North Bend', 'state_id' => 3976],
                ['name' => 'Pacific', 'state_id' => 3976],
                ['name' => 'Preston', 'state_id' => 3976],
                ['name' => 'Ravensdale', 'state_id' => 3976],
                ['name' => 'Redmond', 'state_id' => 3976],
                ['name' => 'Renton', 'state_id' => 3976],
                ['name' => 'Seahurst', 'state_id' => 3976],
                ['name' => 'Snoqualmie', 'state_id' => 3976],
                ['name' => 'Snoqualmie Pass', 'state_id' => 3976],
                ['name' => 'Vashon', 'state_id' => 3976],
                ['name' => 'Woodinville', 'state_id' => 3976],
                ['name' => 'Sammamish', 'state_id' => 3976],
                ['name' => 'Seattle', 'state_id' => 3976],
                ['name' => 'Baring', 'state_id' => 3976],
                ['name' => 'Skykomish', 'state_id' => 3976],
                ['name' => 'Rollingbay', 'state_id' => 3976],
                ['name' => 'Bainbridge Island', 'state_id' => 3976],
                ['name' => 'Bremerton', 'state_id' => 3976],
                ['name' => 'Silverdale', 'state_id' => 3976],
                ['name' => 'Burley', 'state_id' => 3976],
                ['name' => 'Hansville', 'state_id' => 3976],
                ['name' => 'Indianola', 'state_id' => 3976],
                ['name' => 'Keyport', 'state_id' => 3976],
                ['name' => 'Kingston', 'state_id' => 3976],
                ['name' => 'Manchester', 'state_id' => 3976],
                ['name' => 'Olalla', 'state_id' => 3976],
                ['name' => 'Port Gamble', 'state_id' => 3976],
                ['name' => 'Port Orchard', 'state_id' => 3976],
                ['name' => 'Poulsbo', 'state_id' => 3976],
                ['name' => 'Retsil', 'state_id' => 3976],
                ['name' => 'Seabeck', 'state_id' => 3976],
                ['name' => 'South Colby', 'state_id' => 3976],
                ['name' => 'Southworth', 'state_id' => 3976],
                ['name' => 'Suquamish', 'state_id' => 3976],
                ['name' => 'Tracyton', 'state_id' => 3976],
                ['name' => 'Cle Elum', 'state_id' => 3976],
                ['name' => 'Easton', 'state_id' => 3976],
                ['name' => 'Ellensburg', 'state_id' => 3976],
                ['name' => 'Kittitas', 'state_id' => 3976],
                ['name' => 'Ronald', 'state_id' => 3976],
                ['name' => 'Roslyn', 'state_id' => 3976],
                ['name' => 'South Cle Elum', 'state_id' => 3976],
                ['name' => 'Thorp', 'state_id' => 3976],
                ['name' => 'Vantage', 'state_id' => 3976],
                ['name' => 'Appleton', 'state_id' => 3976],
                ['name' => 'Bingen', 'state_id' => 3976],
                ['name' => 'Centerville', 'state_id' => 3976],
                ['name' => 'Dallesport', 'state_id' => 3976],
                ['name' => 'Glenwood', 'state_id' => 3976],
                ['name' => 'Goldendale', 'state_id' => 3976],
                ['name' => 'Husum', 'state_id' => 3976],
                ['name' => 'Klickitat', 'state_id' => 3976],
                ['name' => 'Lyle', 'state_id' => 3976],
                ['name' => 'Trout Lake', 'state_id' => 3976],
                ['name' => 'Wahkiacus', 'state_id' => 3976],
                ['name' => 'White Salmon', 'state_id' => 3976],
                ['name' => 'Wishram', 'state_id' => 3976],
                ['name' => 'Bickleton', 'state_id' => 3976],
                ['name' => 'Roosevelt', 'state_id' => 3976],
                ['name' => 'Glenoma', 'state_id' => 3976],
                ['name' => 'Mineral', 'state_id' => 3976],
                ['name' => 'Morton', 'state_id' => 3976],
                ['name' => 'Packwood', 'state_id' => 3976],
                ['name' => 'Randle', 'state_id' => 3976],
                ['name' => 'Adna', 'state_id' => 3976],
                ['name' => 'Centralia', 'state_id' => 3976],
                ['name' => 'Chehalis', 'state_id' => 3976],
                ['name' => 'Cinebar', 'state_id' => 3976],
                ['name' => 'Curtis', 'state_id' => 3976],
                ['name' => 'Doty', 'state_id' => 3976],
                ['name' => 'Ethel', 'state_id' => 3976],
                ['name' => 'Galvin', 'state_id' => 3976],
                ['name' => 'Mossyrock', 'state_id' => 3976],
                ['name' => 'Napavine', 'state_id' => 3976],
                ['name' => 'Onalaska', 'state_id' => 3976],
                ['name' => 'Pe Ell', 'state_id' => 3976],
                ['name' => 'Salkum', 'state_id' => 3976],
                ['name' => 'Silver Creek', 'state_id' => 3976],
                ['name' => 'Toledo', 'state_id' => 3976],
                ['name' => 'Vader', 'state_id' => 3976],
                ['name' => 'Winlock', 'state_id' => 3976],
                ['name' => 'Edwall', 'state_id' => 3976],
                ['name' => 'Sprague', 'state_id' => 3976],
                ['name' => 'Almira', 'state_id' => 3976],
                ['name' => 'Creston', 'state_id' => 3976],
                ['name' => 'Davenport', 'state_id' => 3976],
                ['name' => 'Harrington', 'state_id' => 3976],
                ['name' => 'Lamona', 'state_id' => 3976],
                ['name' => 'Lincoln', 'state_id' => 3976],
                ['name' => 'Mohler', 'state_id' => 3976],
                ['name' => 'Odessa', 'state_id' => 3976],
                ['name' => 'Wilbur', 'state_id' => 3976],
                ['name' => 'Allyn', 'state_id' => 3976],
                ['name' => 'Belfair', 'state_id' => 3976],
                ['name' => 'Grapeview', 'state_id' => 3976],
                ['name' => 'Hoodsport', 'state_id' => 3976],
                ['name' => 'Lilliwaup', 'state_id' => 3976],
                ['name' => 'Matlock', 'state_id' => 3976],
                ['name' => 'Shelton', 'state_id' => 3976],
                ['name' => 'Tahuya', 'state_id' => 3976],
                ['name' => 'Union', 'state_id' => 3976],
                ['name' => 'Brewster', 'state_id' => 3976],
                ['name' => 'Carlton', 'state_id' => 3976],
                ['name' => 'Conconully', 'state_id' => 3976],
                ['name' => 'Loomis', 'state_id' => 3976],
                ['name' => 'Malott', 'state_id' => 3976],
                ['name' => 'Mazama', 'state_id' => 3976],
                ['name' => 'Methow', 'state_id' => 3976],
                ['name' => 'Okanogan', 'state_id' => 3976],
                ['name' => 'Omak', 'state_id' => 3976],
                ['name' => 'Oroville', 'state_id' => 3976],
                ['name' => 'Pateros', 'state_id' => 3976],
                ['name' => 'Riverside', 'state_id' => 3976],
                ['name' => 'Tonasket', 'state_id' => 3976],
                ['name' => 'Twisp', 'state_id' => 3976],
                ['name' => 'Wauconda', 'state_id' => 3976],
                ['name' => 'Winthrop', 'state_id' => 3976],
                ['name' => 'Coulee Dam', 'state_id' => 3976],
                ['name' => 'Elmer City', 'state_id' => 3976],
                ['name' => 'Nespelem', 'state_id' => 3976],
                ['name' => 'Bay Center', 'state_id' => 3976],
                ['name' => 'Lebam', 'state_id' => 3976],
                ['name' => 'Menlo', 'state_id' => 3976],
                ['name' => 'Raymond', 'state_id' => 3976],
                ['name' => 'South Bend', 'state_id' => 3976],
                ['name' => 'Tokeland', 'state_id' => 3976],
                ['name' => 'Chinook', 'state_id' => 3976],
                ['name' => 'Ilwaco', 'state_id' => 3976],
                ['name' => 'Long Beach', 'state_id' => 3976],
                ['name' => 'Nahcotta', 'state_id' => 3976],
                ['name' => 'Naselle', 'state_id' => 3976],
                ['name' => 'Ocean Park', 'state_id' => 3976],
                ['name' => 'Oysterville', 'state_id' => 3976],
                ['name' => 'Seaview', 'state_id' => 3976],
                ['name' => 'Cusick', 'state_id' => 3976],
                ['name' => 'Ione', 'state_id' => 3976],
                ['name' => 'Metaline', 'state_id' => 3976],
                ['name' => 'Metaline Falls', 'state_id' => 3976],
                ['name' => 'Newport', 'state_id' => 3976],
                ['name' => 'Usk', 'state_id' => 3976],
                ['name' => 'Anderson Island', 'state_id' => 3976],
                ['name' => 'Ashford', 'state_id' => 3976],
                ['name' => 'Buckley', 'state_id' => 3976],
                ['name' => 'Carbonado', 'state_id' => 3976],
                ['name' => 'Dupont', 'state_id' => 3976],
                ['name' => 'Eatonville', 'state_id' => 3976],
                ['name' => 'Gig Harbor', 'state_id' => 3976],
                ['name' => 'Elbe', 'state_id' => 3976],
                ['name' => 'Fox Island', 'state_id' => 3976],
                ['name' => 'Graham', 'state_id' => 3976],
                ['name' => 'Kapowsin', 'state_id' => 3976],
                ['name' => 'La Grande', 'state_id' => 3976],
                ['name' => 'Lakebay', 'state_id' => 3976],
                ['name' => 'Longbranch', 'state_id' => 3976],
                ['name' => 'Sumner', 'state_id' => 3976],
                ['name' => 'Milton', 'state_id' => 3976],
                ['name' => 'Orting', 'state_id' => 3976],
                ['name' => 'Puyallup', 'state_id' => 3976],
                ['name' => 'South Prairie', 'state_id' => 3976],
                ['name' => 'Spanaway', 'state_id' => 3976],
                ['name' => 'Steilacoom', 'state_id' => 3976],
                ['name' => 'Bonney Lake', 'state_id' => 3976],
                ['name' => 'Vaughn', 'state_id' => 3976],
                ['name' => 'Wauna', 'state_id' => 3976],
                ['name' => 'Wilkeson', 'state_id' => 3976],
                ['name' => 'Longmire', 'state_id' => 3976],
                ['name' => 'Paradise Inn', 'state_id' => 3976],
                ['name' => 'Tacoma', 'state_id' => 3976],
                ['name' => 'Camp Murray', 'state_id' => 3976],
                ['name' => 'Mcchord Afb', 'state_id' => 3976],
                ['name' => 'Lakewood', 'state_id' => 3976],
                ['name' => 'University Place', 'state_id' => 3976],
                ['name' => 'Mckenna', 'state_id' => 3976],
                ['name' => 'Roy', 'state_id' => 3976],
                ['name' => 'Blakely Island', 'state_id' => 3976],
                ['name' => 'Deer Harbor', 'state_id' => 3976],
                ['name' => 'Eastsound', 'state_id' => 3976],
                ['name' => 'Friday Harbor', 'state_id' => 3976],
                ['name' => 'Lopez Island', 'state_id' => 3976],
                ['name' => 'Olga', 'state_id' => 3976],
                ['name' => 'Orcas', 'state_id' => 3976],
                ['name' => 'Shaw Island', 'state_id' => 3976],
                ['name' => 'Waldron', 'state_id' => 3976],
                ['name' => 'Anacortes', 'state_id' => 3976],
                ['name' => 'Bow', 'state_id' => 3976],
                ['name' => 'Burlington', 'state_id' => 3976],
                ['name' => 'Clearlake', 'state_id' => 3976],
                ['name' => 'Concrete', 'state_id' => 3976],
                ['name' => 'Conway', 'state_id' => 3976],
                ['name' => 'Hamilton', 'state_id' => 3976],
                ['name' => 'La Conner', 'state_id' => 3976],
                ['name' => 'Lyman', 'state_id' => 3976],
                ['name' => 'Marblemount', 'state_id' => 3976],
                ['name' => 'Mount Vernon', 'state_id' => 3976],
                ['name' => 'Rockport', 'state_id' => 3976],
                ['name' => 'Sedro Woolley', 'state_id' => 3976],
                ['name' => 'Carson', 'state_id' => 3976],
                ['name' => 'North Bonneville', 'state_id' => 3976],
                ['name' => 'Stevenson', 'state_id' => 3976],
                ['name' => 'Underwood', 'state_id' => 3976],
                ['name' => 'Edmonds', 'state_id' => 3976],
                ['name' => 'Lynnwood', 'state_id' => 3976],
                ['name' => 'Mountlake Terrace', 'state_id' => 3976],
                ['name' => 'Mill Creek', 'state_id' => 3976],
                ['name' => 'Everett', 'state_id' => 3976],
                ['name' => 'Arlington', 'state_id' => 3976],
                ['name' => 'Darrington', 'state_id' => 3976],
                ['name' => 'Gold Bar', 'state_id' => 3976],
                ['name' => 'Granite Falls', 'state_id' => 3976],
                ['name' => 'Index', 'state_id' => 3976],
                ['name' => 'Lake Stevens', 'state_id' => 3976],
                ['name' => 'North Lakewood', 'state_id' => 3976],
                ['name' => 'Marysville', 'state_id' => 3976],
                ['name' => 'Monroe', 'state_id' => 3976],
                ['name' => 'Mukilteo', 'state_id' => 3976],
                ['name' => 'Camano Island', 'state_id' => 3976],
                ['name' => 'Silvana', 'state_id' => 3976],
                ['name' => 'Snohomish', 'state_id' => 3976],
                ['name' => 'Stanwood', 'state_id' => 3976],
                ['name' => 'Startup', 'state_id' => 3976],
                ['name' => 'Sultan', 'state_id' => 3976],
                ['name' => 'Airway Heights', 'state_id' => 3976],
                ['name' => 'Chattaroy', 'state_id' => 3976],
                ['name' => 'Cheney', 'state_id' => 3976],
                ['name' => 'Colbert', 'state_id' => 3976],
                ['name' => 'Deer Park', 'state_id' => 3976],
                ['name' => 'Elk', 'state_id' => 3976],
                ['name' => 'Fairchild Air Force Base', 'state_id' => 3976],
                ['name' => 'Fairfield', 'state_id' => 3976],
                ['name' => 'Four Lakes', 'state_id' => 3976],
                ['name' => 'Greenacres', 'state_id' => 3976],
                ['name' => 'Latah', 'state_id' => 3976],
                ['name' => 'Liberty Lake', 'state_id' => 3976],
                ['name' => 'Marshall', 'state_id' => 3976],
                ['name' => 'Mead', 'state_id' => 3976],
                ['name' => 'Medical Lake', 'state_id' => 3976],
                ['name' => 'Mica', 'state_id' => 3976],
                ['name' => 'Newman Lake', 'state_id' => 3976],
                ['name' => 'Nine Mile Falls', 'state_id' => 3976],
                ['name' => 'Otis Orchards', 'state_id' => 3976],
                ['name' => 'Reardan', 'state_id' => 3976],
                ['name' => 'Rockford', 'state_id' => 3976],
                ['name' => 'Spangle', 'state_id' => 3976],
                ['name' => 'Valleyford', 'state_id' => 3976],
                ['name' => 'Veradale', 'state_id' => 3976],
                ['name' => 'Waverly', 'state_id' => 3976],
                ['name' => 'Spokane', 'state_id' => 3976],
                ['name' => 'Ford', 'state_id' => 3976],
                ['name' => 'Tumtum', 'state_id' => 3976],
                ['name' => 'Wellpinit', 'state_id' => 3976],
                ['name' => 'Addy', 'state_id' => 3976],
                ['name' => 'Chewelah', 'state_id' => 3976],
                ['name' => 'Clayton', 'state_id' => 3976],
                ['name' => 'Colville', 'state_id' => 3976],
                ['name' => 'Evans', 'state_id' => 3976],
                ['name' => 'Fruitland', 'state_id' => 3976],
                ['name' => 'Gifford', 'state_id' => 3976],
                ['name' => 'Hunters', 'state_id' => 3976],
                ['name' => 'Kettle Falls', 'state_id' => 3976],
                ['name' => 'Loon Lake', 'state_id' => 3976],
                ['name' => 'Marcus', 'state_id' => 3976],
                ['name' => 'Northport', 'state_id' => 3976],
                ['name' => 'Rice', 'state_id' => 3976],
                ['name' => 'Springdale', 'state_id' => 3976],
                ['name' => 'Valley', 'state_id' => 3976],
                ['name' => 'Olympia', 'state_id' => 3976],
                ['name' => 'Lacey', 'state_id' => 3976],
                ['name' => 'Tumwater', 'state_id' => 3976],
                ['name' => 'Bucoda', 'state_id' => 3976],
                ['name' => 'East Olympia', 'state_id' => 3976],
                ['name' => 'Littlerock', 'state_id' => 3976],
                ['name' => 'Rainier', 'state_id' => 3976],
                ['name' => 'Rochester', 'state_id' => 3976],
                ['name' => 'Tenino', 'state_id' => 3976],
                ['name' => 'Yelm', 'state_id' => 3976],
                ['name' => 'Cathlamet', 'state_id' => 3976],
                ['name' => 'Grays River', 'state_id' => 3976],
                ['name' => 'Rosburg', 'state_id' => 3976],
                ['name' => 'Skamokawa', 'state_id' => 3976],
                ['name' => 'Burbank', 'state_id' => 3976],
                ['name' => 'College Place', 'state_id' => 3976],
                ['name' => 'Dixie', 'state_id' => 3976],
                ['name' => 'Prescott', 'state_id' => 3976],
                ['name' => 'Touchet', 'state_id' => 3976],
                ['name' => 'Waitsburg', 'state_id' => 3976],
                ['name' => 'Walla Walla', 'state_id' => 3976],
                ['name' => 'Wallula', 'state_id' => 3976],
                ['name' => 'Acme', 'state_id' => 3976],
                ['name' => 'Bellingham', 'state_id' => 3976],
                ['name' => 'Blaine', 'state_id' => 3976],
                ['name' => 'Custer', 'state_id' => 3976],
                ['name' => 'Deming', 'state_id' => 3976],
                ['name' => 'Everson', 'state_id' => 3976],
                ['name' => 'Ferndale', 'state_id' => 3976],
                ['name' => 'Lummi Island', 'state_id' => 3976],
                ['name' => 'Lynden', 'state_id' => 3976],
                ['name' => 'Maple Falls', 'state_id' => 3976],
                ['name' => 'Nooksack', 'state_id' => 3976],
                ['name' => 'Point Roberts', 'state_id' => 3976],
                ['name' => 'Sumas', 'state_id' => 3976],
                ['name' => 'Lamont', 'state_id' => 3976],
                ['name' => 'Tekoa', 'state_id' => 3976],
                ['name' => 'Albion', 'state_id' => 3976],
                ['name' => 'Belmont', 'state_id' => 3976],
                ['name' => 'Colfax', 'state_id' => 3976],
                ['name' => 'Colton', 'state_id' => 3976],
                ['name' => 'Endicott', 'state_id' => 3976],
                ['name' => 'Farmington', 'state_id' => 3976],
                ['name' => 'Garfield', 'state_id' => 3976],
                ['name' => 'Hay', 'state_id' => 3976],
                ['name' => 'Lacrosse', 'state_id' => 3976],
                ['name' => 'Malden', 'state_id' => 3976],
                ['name' => 'Oakesdale', 'state_id' => 3976],
                ['name' => 'Palouse', 'state_id' => 3976],
                ['name' => 'Pullman', 'state_id' => 3976],
                ['name' => 'Rosalia', 'state_id' => 3976],
                ['name' => 'Saint John', 'state_id' => 3976],
                ['name' => 'Steptoe', 'state_id' => 3976],
                ['name' => 'Thornton', 'state_id' => 3976],
                ['name' => 'Uniontown', 'state_id' => 3976],
                ['name' => 'Hooper', 'state_id' => 3976],
                ['name' => 'Yakima', 'state_id' => 3976],
                ['name' => 'Brownstown', 'state_id' => 3976],
                ['name' => 'Buena', 'state_id' => 3976],
                ['name' => 'Cowiche', 'state_id' => 3976],
                ['name' => 'Grandview', 'state_id' => 3976],
                ['name' => 'Granger', 'state_id' => 3976],
                ['name' => 'Harrah', 'state_id' => 3976],
                ['name' => 'Mabton', 'state_id' => 3976],
                ['name' => 'Moxee', 'state_id' => 3976],
                ['name' => 'Naches', 'state_id' => 3976],
                ['name' => 'Outlook', 'state_id' => 3976],
                ['name' => 'Parker', 'state_id' => 3976],
                ['name' => 'Selah', 'state_id' => 3976],
                ['name' => 'Sunnyside', 'state_id' => 3976],
                ['name' => 'Tieton', 'state_id' => 3976],
                ['name' => 'Toppenish', 'state_id' => 3976],
                ['name' => 'Wapato', 'state_id' => 3976],
                ['name' => 'White Swan', 'state_id' => 3976],
                ['name' => 'Zillah', 'state_id' => 3976]
            ]);
        }
    }
}
