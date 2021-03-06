<?php

use Illuminate\Database\Seeder;

class state32TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Create Cities for the state of MJ - New Jersey.
        //If the table 'cities' exists, insert the data to the table.
        if (DB::table('cities')->get()->count() >= 0) {
            DB::table('cities')->insert([
                ['name' => 'Hammonton', 'state_id' => 3954],
                ['name' => 'Absecon', 'state_id' => 3954],
                ['name' => 'Brigantine', 'state_id' => 3954],
                ['name' => 'Cologne', 'state_id' => 3954],
                ['name' => 'Egg Harbor City', 'state_id' => 3954],
                ['name' => 'Elwood', 'state_id' => 3954],
                ['name' => 'Leeds Point', 'state_id' => 3954],
                ['name' => 'Linwood', 'state_id' => 3954],
                ['name' => 'Northfield', 'state_id' => 3954],
                ['name' => 'Oceanville', 'state_id' => 3954],
                ['name' => 'Pleasantville', 'state_id' => 3954],
                ['name' => 'Egg Harbor Township', 'state_id' => 3954],
                ['name' => 'Pomona', 'state_id' => 3954],
                ['name' => 'Port Republic', 'state_id' => 3954],
                ['name' => 'Somers Point', 'state_id' => 3954],
                ['name' => 'Buena', 'state_id' => 3954],
                ['name' => 'Dorothy', 'state_id' => 3954],
                ['name' => 'Estell Manor', 'state_id' => 3954],
                ['name' => 'Landisville', 'state_id' => 3954],
                ['name' => 'Mays Landing', 'state_id' => 3954],
                ['name' => 'Milmay', 'state_id' => 3954],
                ['name' => 'Minotola', 'state_id' => 3954],
                ['name' => 'Mizpah', 'state_id' => 3954],
                ['name' => 'Newtonville', 'state_id' => 3954],
                ['name' => 'Richland', 'state_id' => 3954],
                ['name' => 'Atlantic City', 'state_id' => 3954],
                ['name' => 'Margate City', 'state_id' => 3954],
                ['name' => 'Longport', 'state_id' => 3954],
                ['name' => 'Ventnor City', 'state_id' => 3954],
                ['name' => 'Cliffside Park', 'state_id' => 3954],
                ['name' => 'Edgewater', 'state_id' => 3954],
                ['name' => 'Fairview', 'state_id' => 3954],
                ['name' => 'Fort Lee', 'state_id' => 3954],
                ['name' => 'Garfield', 'state_id' => 3954],
                ['name' => 'North Arlington', 'state_id' => 3954],
                ['name' => 'Wallington', 'state_id' => 3954],
                ['name' => 'Rutherford', 'state_id' => 3954],
                ['name' => 'Lyndhurst', 'state_id' => 3954],
                ['name' => 'Carlstadt', 'state_id' => 3954],
                ['name' => 'East Rutherford', 'state_id' => 3954],
                ['name' => 'Moonachie', 'state_id' => 3954],
                ['name' => 'Wood Ridge', 'state_id' => 3954],
                ['name' => 'Allendale', 'state_id' => 3954],
                ['name' => 'Elmwood Park', 'state_id' => 3954],
                ['name' => 'Fair Lawn', 'state_id' => 3954],
                ['name' => 'Franklin Lakes', 'state_id' => 3954],
                ['name' => 'Ho Ho Kus', 'state_id' => 3954],
                ['name' => 'Mahwah', 'state_id' => 3954],
                ['name' => 'Midland Park', 'state_id' => 3954],
                ['name' => 'Oakland', 'state_id' => 3954],
                ['name' => 'Ramsey', 'state_id' => 3954],
                ['name' => 'Ridgewood', 'state_id' => 3954],
                ['name' => 'Glen Rock', 'state_id' => 3954],
                ['name' => 'Saddle River', 'state_id' => 3954],
                ['name' => 'Waldwick', 'state_id' => 3954],
                ['name' => 'Wyckoff', 'state_id' => 3954],
                ['name' => 'Hackensack', 'state_id' => 3954],
                ['name' => 'Bogota', 'state_id' => 3954],
                ['name' => 'Hasbrouck Heights', 'state_id' => 3954],
                ['name' => 'Leonia', 'state_id' => 3954],
                ['name' => 'South Hackensack', 'state_id' => 3954],
                ['name' => 'Maywood', 'state_id' => 3954],
                ['name' => 'Teterboro', 'state_id' => 3954],
                ['name' => 'Alpine', 'state_id' => 3954],
                ['name' => 'Bergenfield', 'state_id' => 3954],
                ['name' => 'Closter', 'state_id' => 3954],
                ['name' => 'Cresskill', 'state_id' => 3954],
                ['name' => 'Demarest', 'state_id' => 3954],
                ['name' => 'Dumont', 'state_id' => 3954],
                ['name' => 'Emerson', 'state_id' => 3954],
                ['name' => 'Englewood', 'state_id' => 3954],
                ['name' => 'Englewood Cliffs', 'state_id' => 3954],
                ['name' => 'Harrington Park', 'state_id' => 3954],
                ['name' => 'Haworth', 'state_id' => 3954],
                ['name' => 'Hillsdale', 'state_id' => 3954],
                ['name' => 'Little Ferry', 'state_id' => 3954],
                ['name' => 'Lodi', 'state_id' => 3954],
                ['name' => 'Montvale', 'state_id' => 3954],
                ['name' => 'New Milford', 'state_id' => 3954],
                ['name' => 'Northvale', 'state_id' => 3954],
                ['name' => 'Norwood', 'state_id' => 3954],
                ['name' => 'Oradell', 'state_id' => 3954],
                ['name' => 'Palisades Park', 'state_id' => 3954],
                ['name' => 'Paramus', 'state_id' => 3954],
                ['name' => 'Park Ridge', 'state_id' => 3954],
                ['name' => 'Ridgefield', 'state_id' => 3954],
                ['name' => 'Ridgefield Park', 'state_id' => 3954],
                ['name' => 'River Edge', 'state_id' => 3954],
                ['name' => 'Rochelle Park', 'state_id' => 3954],
                ['name' => 'Saddle Brook', 'state_id' => 3954],
                ['name' => 'Teaneck', 'state_id' => 3954],
                ['name' => 'Tenafly', 'state_id' => 3954],
                ['name' => 'Westwood', 'state_id' => 3954],
                ['name' => 'Township Of Washington', 'state_id' => 3954],
                ['name' => 'Woodcliff Lake', 'state_id' => 3954],
                ['name' => 'Beverly', 'state_id' => 3954],
                ['name' => 'Birmingham', 'state_id' => 3954],
                ['name' => 'Browns Mills', 'state_id' => 3954],
                ['name' => 'Burlington', 'state_id' => 3954],
                ['name' => 'Chatsworth', 'state_id' => 3954],
                ['name' => 'Columbus', 'state_id' => 3954],
                ['name' => 'Hainesport', 'state_id' => 3954],
                ['name' => 'Jobstown', 'state_id' => 3954],
                ['name' => 'Juliustown', 'state_id' => 3954],
                ['name' => 'Willingboro', 'state_id' => 3954],
                ['name' => 'Lumberton', 'state_id' => 3954],
                ['name' => 'Maple Shade', 'state_id' => 3954],
                ['name' => 'Marlton', 'state_id' => 3954],
                ['name' => 'Mount Laurel', 'state_id' => 3954],
                ['name' => 'Medford', 'state_id' => 3954],
                ['name' => 'Moorestown', 'state_id' => 3954],
                ['name' => 'Mount Holly', 'state_id' => 3954],
                ['name' => 'New Lisbon', 'state_id' => 3954],
                ['name' => 'Palmyra', 'state_id' => 3954],
                ['name' => 'Pemberton', 'state_id' => 3954],
                ['name' => 'Rancocas', 'state_id' => 3954],
                ['name' => 'Riverside', 'state_id' => 3954],
                ['name' => 'Riverton', 'state_id' => 3954],
                ['name' => 'Vincentown', 'state_id' => 3954],
                ['name' => 'New Gretna', 'state_id' => 3954],
                ['name' => 'Bordentown', 'state_id' => 3954],
                ['name' => 'Cookstown', 'state_id' => 3954],
                ['name' => 'Chesterfield', 'state_id' => 3954],
                ['name' => 'Florence', 'state_id' => 3954],
                ['name' => 'Roebling', 'state_id' => 3954],
                ['name' => 'Wrightstown', 'state_id' => 3954],
                ['name' => 'Joint Base Mdl', 'state_id' => 3954],
                ['name' => 'Cherry Hill', 'state_id' => 3954],
                ['name' => 'Atco', 'state_id' => 3954],
                ['name' => 'Barrington', 'state_id' => 3954],
                ['name' => 'Berlin', 'state_id' => 3954],
                ['name' => 'Blackwood', 'state_id' => 3954],
                ['name' => 'Cedar Brook', 'state_id' => 3954],
                ['name' => 'Clementon', 'state_id' => 3954],
                ['name' => 'Gibbsboro', 'state_id' => 3954],
                ['name' => 'Glendora', 'state_id' => 3954],
                ['name' => 'Gloucester City', 'state_id' => 3954],
                ['name' => 'Bellmawr', 'state_id' => 3954],
                ['name' => 'Haddonfield', 'state_id' => 3954],
                ['name' => 'Haddon Heights', 'state_id' => 3954],
                ['name' => 'Voorhees', 'state_id' => 3954],
                ['name' => 'Lawnside', 'state_id' => 3954],
                ['name' => 'Magnolia', 'state_id' => 3954],
                ['name' => 'Mount Ephraim', 'state_id' => 3954],
                ['name' => 'Runnemede', 'state_id' => 3954],
                ['name' => 'Sicklerville', 'state_id' => 3954],
                ['name' => 'Somerdale', 'state_id' => 3954],
                ['name' => 'Stratford', 'state_id' => 3954],
                ['name' => 'Waterford Works', 'state_id' => 3954],
                ['name' => 'West Berlin', 'state_id' => 3954],
                ['name' => 'Winslow', 'state_id' => 3954],
                ['name' => 'Camden', 'state_id' => 3954],
                ['name' => 'Audubon', 'state_id' => 3954],
                ['name' => 'Oaklyn', 'state_id' => 3954],
                ['name' => 'Collingswood', 'state_id' => 3954],
                ['name' => 'Merchantville', 'state_id' => 3954],
                ['name' => 'Pennsauken', 'state_id' => 3954],
                ['name' => 'Avalon', 'state_id' => 3954],
                ['name' => 'Cape May', 'state_id' => 3954],
                ['name' => 'Cape May Court House', 'state_id' => 3954],
                ['name' => 'Cape May Point', 'state_id' => 3954],
                ['name' => 'Dennisville', 'state_id' => 3954],
                ['name' => 'Goshen', 'state_id' => 3954],
                ['name' => 'Green Creek', 'state_id' => 3954],
                ['name' => 'Marmora', 'state_id' => 3954],
                ['name' => 'Ocean City', 'state_id' => 3954],
                ['name' => 'Ocean View', 'state_id' => 3954],
                ['name' => 'Rio Grande', 'state_id' => 3954],
                ['name' => 'Sea Isle City', 'state_id' => 3954],
                ['name' => 'South Dennis', 'state_id' => 3954],
                ['name' => 'South Seaville', 'state_id' => 3954],
                ['name' => 'Stone Harbor', 'state_id' => 3954],
                ['name' => 'Strathmere', 'state_id' => 3954],
                ['name' => 'Tuckahoe', 'state_id' => 3954],
                ['name' => 'Villas', 'state_id' => 3954],
                ['name' => 'Whitesboro', 'state_id' => 3954],
                ['name' => 'Wildwood', 'state_id' => 3954],
                ['name' => 'Woodbine', 'state_id' => 3954],
                ['name' => 'Bridgeton', 'state_id' => 3954],
                ['name' => 'Cedarville', 'state_id' => 3954],
                ['name' => 'Deerfield Street', 'state_id' => 3954],
                ['name' => 'Delmont', 'state_id' => 3954],
                ['name' => 'Dividing Creek', 'state_id' => 3954],
                ['name' => 'Dorchester', 'state_id' => 3954],
                ['name' => 'Fairton', 'state_id' => 3954],
                ['name' => 'Fortescue', 'state_id' => 3954],
                ['name' => 'Greenwich', 'state_id' => 3954],
                ['name' => 'Heislerville', 'state_id' => 3954],
                ['name' => 'Leesburg', 'state_id' => 3954],
                ['name' => 'Mauricetown', 'state_id' => 3954],
                ['name' => 'Millville', 'state_id' => 3954],
                ['name' => 'Newport', 'state_id' => 3954],
                ['name' => 'Port Elizabeth', 'state_id' => 3954],
                ['name' => 'Port Norris', 'state_id' => 3954],
                ['name' => 'Rosenhayn', 'state_id' => 3954],
                ['name' => 'Shiloh', 'state_id' => 3954],
                ['name' => 'Vineland', 'state_id' => 3954],
                ['name' => 'Bloomfield', 'state_id' => 3954],
                ['name' => 'Fairfield', 'state_id' => 3954],
                ['name' => 'Caldwell', 'state_id' => 3954],
                ['name' => 'Cedar Grove', 'state_id' => 3954],
                ['name' => 'East Orange', 'state_id' => 3954],
                ['name' => 'Essex Fells', 'state_id' => 3954],
                ['name' => 'Glen Ridge', 'state_id' => 3954],
                ['name' => 'Livingston', 'state_id' => 3954],
                ['name' => 'Maplewood', 'state_id' => 3954],
                ['name' => 'Millburn', 'state_id' => 3954],
                ['name' => 'Montclair', 'state_id' => 3954],
                ['name' => 'Verona', 'state_id' => 3954],
                ['name' => 'Orange', 'state_id' => 3954],
                ['name' => 'West Orange', 'state_id' => 3954],
                ['name' => 'Roseland', 'state_id' => 3954],
                ['name' => 'Short Hills', 'state_id' => 3954],
                ['name' => 'South Orange', 'state_id' => 3954],
                ['name' => 'Newark', 'state_id' => 3954],
                ['name' => 'Belleville', 'state_id' => 3954],
                ['name' => 'Nutley', 'state_id' => 3954],
                ['name' => 'Irvington', 'state_id' => 3954],
                ['name' => 'Bridgeport', 'state_id' => 3954],
                ['name' => 'Clarksboro', 'state_id' => 3954],
                ['name' => 'Ewan', 'state_id' => 3954],
                ['name' => 'Gibbstown', 'state_id' => 3954],
                ['name' => 'Glassboro', 'state_id' => 3954],
                ['name' => 'Grenloch', 'state_id' => 3954],
                ['name' => 'Harrisonville', 'state_id' => 3954],
                ['name' => 'Mantua', 'state_id' => 3954],
                ['name' => 'Mickleton', 'state_id' => 3954],
                ['name' => 'Mount Royal', 'state_id' => 3954],
                ['name' => 'Mullica Hill', 'state_id' => 3954],
                ['name' => 'National Park', 'state_id' => 3954],
                ['name' => 'Paulsboro', 'state_id' => 3954],
                ['name' => 'Pitman', 'state_id' => 3954],
                ['name' => 'Richwood', 'state_id' => 3954],
                ['name' => 'Sewell', 'state_id' => 3954],
                ['name' => 'Swedesboro', 'state_id' => 3954],
                ['name' => 'Thorofare', 'state_id' => 3954],
                ['name' => 'Wenonah', 'state_id' => 3954],
                ['name' => 'Westville', 'state_id' => 3954],
                ['name' => 'Williamstown', 'state_id' => 3954],
                ['name' => 'Deptford', 'state_id' => 3954],
                ['name' => 'Woodbury Heights', 'state_id' => 3954],
                ['name' => 'Clayton', 'state_id' => 3954],
                ['name' => 'Franklinville', 'state_id' => 3954],
                ['name' => 'Malaga', 'state_id' => 3954],
                ['name' => 'Monroeville', 'state_id' => 3954],
                ['name' => 'Newfield', 'state_id' => 3954],
                ['name' => 'Bayonne', 'state_id' => 3954],
                ['name' => 'Harrison', 'state_id' => 3954],
                ['name' => 'Hoboken', 'state_id' => 3954],
                ['name' => 'Kearny', 'state_id' => 3954],
                ['name' => 'North Bergen', 'state_id' => 3954],
                ['name' => 'Weehawken', 'state_id' => 3954],
                ['name' => 'Union City', 'state_id' => 3954],
                ['name' => 'West New York', 'state_id' => 3954],
                ['name' => 'Secaucus', 'state_id' => 3954],
                ['name' => 'Jersey City', 'state_id' => 3954],
                ['name' => 'Califon', 'state_id' => 3954],
                ['name' => 'Pottersville', 'state_id' => 3954],
                ['name' => 'Lambertville', 'state_id' => 3954],
                ['name' => 'Ringoes', 'state_id' => 3954],
                ['name' => 'Rosemont', 'state_id' => 3954],
                ['name' => 'Sergeantsville', 'state_id' => 3954],
                ['name' => 'Stockton', 'state_id' => 3954],
                ['name' => 'Annandale', 'state_id' => 3954],
                ['name' => 'Baptistown', 'state_id' => 3954],
                ['name' => 'Bloomsbury', 'state_id' => 3954],
                ['name' => 'Clinton', 'state_id' => 3954],
                ['name' => 'Flemington', 'state_id' => 3954],
                ['name' => 'Frenchtown', 'state_id' => 3954],
                ['name' => 'Glen Gardner', 'state_id' => 3954],
                ['name' => 'Hampton', 'state_id' => 3954],
                ['name' => 'High Bridge', 'state_id' => 3954],
                ['name' => 'Lebanon', 'state_id' => 3954],
                ['name' => 'Little York', 'state_id' => 3954],
                ['name' => 'Milford', 'state_id' => 3954],
                ['name' => 'Oldwick', 'state_id' => 3954],
                ['name' => 'Pittstown', 'state_id' => 3954],
                ['name' => 'Quakertown', 'state_id' => 3954],
                ['name' => 'Readington', 'state_id' => 3954],
                ['name' => 'Stanton', 'state_id' => 3954],
                ['name' => 'Three Bridges', 'state_id' => 3954],
                ['name' => 'Whitehouse', 'state_id' => 3954],
                ['name' => 'Whitehouse Station', 'state_id' => 3954],
                ['name' => 'Hightstown', 'state_id' => 3954],
                ['name' => 'Hopewell', 'state_id' => 3954],
                ['name' => 'Pennington', 'state_id' => 3954],
                ['name' => 'Princeton', 'state_id' => 3954],
                ['name' => 'Princeton Junction', 'state_id' => 3954],
                ['name' => 'Titusville', 'state_id' => 3954],
                ['name' => 'Windsor', 'state_id' => 3954],
                ['name' => 'Trenton', 'state_id' => 3954],
                ['name' => 'Lawrence Township', 'state_id' => 3954],
                ['name' => 'Avenel', 'state_id' => 3954],
                ['name' => 'Carteret', 'state_id' => 3954],
                ['name' => 'Port Reading', 'state_id' => 3954],
                ['name' => 'Colonia', 'state_id' => 3954],
                ['name' => 'Sewaren', 'state_id' => 3954],
                ['name' => 'South Plainfield', 'state_id' => 3954],
                ['name' => 'Woodbridge', 'state_id' => 3954],
                ['name' => 'Cranbury', 'state_id' => 3954],
                ['name' => 'Plainsboro', 'state_id' => 3954],
                ['name' => 'Dayton', 'state_id' => 3954],
                ['name' => 'Dunellen', 'state_id' => 3954],
                ['name' => 'East Brunswick', 'state_id' => 3954],
                ['name' => 'Edison', 'state_id' => 3954],
                ['name' => 'Kendall Park', 'state_id' => 3954],
                ['name' => 'Helmetta', 'state_id' => 3954],
                ['name' => 'Iselin', 'state_id' => 3954],
                ['name' => 'Monroe Township', 'state_id' => 3954],
                ['name' => 'Keasbey', 'state_id' => 3954],
                ['name' => 'Metuchen', 'state_id' => 3954],
                ['name' => 'Middlesex', 'state_id' => 3954],
                ['name' => 'Milltown', 'state_id' => 3954],
                ['name' => 'Monmouth Junction', 'state_id' => 3954],
                ['name' => 'Piscataway', 'state_id' => 3954],
                ['name' => 'Old Bridge', 'state_id' => 3954],
                ['name' => 'Parlin', 'state_id' => 3954],
                ['name' => 'Perth Amboy', 'state_id' => 3954],
                ['name' => 'Fords', 'state_id' => 3954],
                ['name' => 'Sayreville', 'state_id' => 3954],
                ['name' => 'South Amboy', 'state_id' => 3954],
                ['name' => 'South River', 'state_id' => 3954],
                ['name' => 'Spotswood', 'state_id' => 3954],
                ['name' => 'New Brunswick', 'state_id' => 3954],
                ['name' => 'North Brunswick', 'state_id' => 3954],
                ['name' => 'Highland Park', 'state_id' => 3954],
                ['name' => 'Red Bank', 'state_id' => 3954],
                ['name' => 'Shrewsbury', 'state_id' => 3954],
                ['name' => 'Fort Monmouth', 'state_id' => 3954],
                ['name' => 'Fair Haven', 'state_id' => 3954],
                ['name' => 'Adelphia', 'state_id' => 3954],
                ['name' => 'Allenhurst', 'state_id' => 3954],
                ['name' => 'Asbury Park', 'state_id' => 3954],
                ['name' => 'Belmar', 'state_id' => 3954],
                ['name' => 'Atlantic Highlands', 'state_id' => 3954],
                ['name' => 'Avon By The Sea', 'state_id' => 3954],
                ['name' => 'Belford', 'state_id' => 3954],
                ['name' => 'Bradley Beach', 'state_id' => 3954],
                ['name' => 'Cliffwood', 'state_id' => 3954],
                ['name' => 'Colts Neck', 'state_id' => 3954],
                ['name' => 'Deal', 'state_id' => 3954],
                ['name' => 'Eatontown', 'state_id' => 3954],
                ['name' => 'Englishtown', 'state_id' => 3954],
                ['name' => 'Farmingdale', 'state_id' => 3954],
                ['name' => 'Freehold', 'state_id' => 3954],
                ['name' => 'Hazlet', 'state_id' => 3954],
                ['name' => 'Howell', 'state_id' => 3954],
                ['name' => 'Highlands', 'state_id' => 3954],
                ['name' => 'Holmdel', 'state_id' => 3954],
                ['name' => 'Keansburg', 'state_id' => 3954],
                ['name' => 'Keyport', 'state_id' => 3954],
                ['name' => 'Leonardo', 'state_id' => 3954],
                ['name' => 'Lincroft', 'state_id' => 3954],
                ['name' => 'Little Silver', 'state_id' => 3954],
                ['name' => 'Long Branch', 'state_id' => 3954],
                ['name' => 'Marlboro', 'state_id' => 3954],
                ['name' => 'Matawan', 'state_id' => 3954],
                ['name' => 'Middletown', 'state_id' => 3954],
                ['name' => 'Monmouth Beach', 'state_id' => 3954],
                ['name' => 'Morganville', 'state_id' => 3954],
                ['name' => 'Navesink', 'state_id' => 3954],
                ['name' => 'Neptune', 'state_id' => 3954],
                ['name' => 'Oakhurst', 'state_id' => 3954],
                ['name' => 'Ocean Grove', 'state_id' => 3954],
                ['name' => 'Oceanport', 'state_id' => 3954],
                ['name' => 'Port Monmouth', 'state_id' => 3954],
                ['name' => 'Rumson', 'state_id' => 3954],
                ['name' => 'Spring Lake', 'state_id' => 3954],
                ['name' => 'Tennent', 'state_id' => 3954],
                ['name' => 'West Long Branch', 'state_id' => 3954],
                ['name' => 'Wickatunk', 'state_id' => 3954],
                ['name' => 'Allentown', 'state_id' => 3954],
                ['name' => 'Millstone Township', 'state_id' => 3954],
                ['name' => 'Cream Ridge', 'state_id' => 3954],
                ['name' => 'Imlaystown', 'state_id' => 3954],
                ['name' => 'Roosevelt', 'state_id' => 3954],
                ['name' => 'Allenwood', 'state_id' => 3954],
                ['name' => 'Brielle', 'state_id' => 3954],
                ['name' => 'Manasquan', 'state_id' => 3954],
                ['name' => 'Sea Girt', 'state_id' => 3954],
                ['name' => 'Boonton', 'state_id' => 3954],
                ['name' => 'Lake Hiawatha', 'state_id' => 3954],
                ['name' => 'Lincoln Park', 'state_id' => 3954],
                ['name' => 'Montville', 'state_id' => 3954],
                ['name' => 'Mountain Lakes', 'state_id' => 3954],
                ['name' => 'Parsippany', 'state_id' => 3954],
                ['name' => 'Pine Brook', 'state_id' => 3954],
                ['name' => 'Towaco', 'state_id' => 3954],
                ['name' => 'Butler', 'state_id' => 3954],
                ['name' => 'Pequannock', 'state_id' => 3954],
                ['name' => 'Pompton Plains', 'state_id' => 3954],
                ['name' => 'Riverdale', 'state_id' => 3954],
                ['name' => 'Dover', 'state_id' => 3954],
                ['name' => 'Mine Hill', 'state_id' => 3954],
                ['name' => 'Picatinny Arsenal', 'state_id' => 3954],
                ['name' => 'Budd Lake', 'state_id' => 3954],
                ['name' => 'Denville', 'state_id' => 3954],
                ['name' => 'Flanders', 'state_id' => 3954],
                ['name' => 'Hibernia', 'state_id' => 3954],
                ['name' => 'Ironia', 'state_id' => 3954],
                ['name' => 'Kenvil', 'state_id' => 3954],
                ['name' => 'Lake Hopatcong', 'state_id' => 3954],
                ['name' => 'Landing', 'state_id' => 3954],
                ['name' => 'Ledgewood', 'state_id' => 3954],
                ['name' => 'Long Valley', 'state_id' => 3954],
                ['name' => 'Mount Arlington', 'state_id' => 3954],
                ['name' => 'Netcong', 'state_id' => 3954],
                ['name' => 'Rockaway', 'state_id' => 3954],
                ['name' => 'Randolph', 'state_id' => 3954],
                ['name' => 'Schooleys Mountain', 'state_id' => 3954],
                ['name' => 'Succasunna', 'state_id' => 3954],
                ['name' => 'Mount Tabor', 'state_id' => 3954],
                ['name' => 'Wharton', 'state_id' => 3954],
                ['name' => 'Brookside', 'state_id' => 3954],
                ['name' => 'Cedar Knolls', 'state_id' => 3954],
                ['name' => 'Chatham', 'state_id' => 3954],
                ['name' => 'Chester', 'state_id' => 3954],
                ['name' => 'Florham Park', 'state_id' => 3954],
                ['name' => 'Gillette', 'state_id' => 3954],
                ['name' => 'Green Village', 'state_id' => 3954],
                ['name' => 'East Hanover', 'state_id' => 3954],
                ['name' => 'Madison', 'state_id' => 3954],
                ['name' => 'Mendham', 'state_id' => 3954],
                ['name' => 'Millington', 'state_id' => 3954],
                ['name' => 'Morris Plains', 'state_id' => 3954],
                ['name' => 'Morristown', 'state_id' => 3954],
                ['name' => 'Convent Station', 'state_id' => 3954],
                ['name' => 'Mount Freedom', 'state_id' => 3954],
                ['name' => 'New Vernon', 'state_id' => 3954],
                ['name' => 'Stirling', 'state_id' => 3954],
                ['name' => 'Whippany', 'state_id' => 3954],
                ['name' => 'Barnegat', 'state_id' => 3954],
                ['name' => 'Barnegat Light', 'state_id' => 3954],
                ['name' => 'Beach Haven', 'state_id' => 3954],
                ['name' => 'Manahawkin', 'state_id' => 3954],
                ['name' => 'Tuckerton', 'state_id' => 3954],
                ['name' => 'West Creek', 'state_id' => 3954],
                ['name' => 'Jackson', 'state_id' => 3954],
                ['name' => 'New Egypt', 'state_id' => 3954],
                ['name' => 'Lakewood', 'state_id' => 3954],
                ['name' => 'Bayville', 'state_id' => 3954],
                ['name' => 'Beachwood', 'state_id' => 3954],
                ['name' => 'Brick', 'state_id' => 3954],
                ['name' => 'Forked River', 'state_id' => 3954],
                ['name' => 'Island Heights', 'state_id' => 3954],
                ['name' => 'Lakehurst', 'state_id' => 3954],
                ['name' => 'Lanoka Harbor', 'state_id' => 3954],
                ['name' => 'Lavallette', 'state_id' => 3954],
                ['name' => 'Mantoloking', 'state_id' => 3954],
                ['name' => 'Normandy Beach', 'state_id' => 3954],
                ['name' => 'Ocean Gate', 'state_id' => 3954],
                ['name' => 'Pine Beach', 'state_id' => 3954],
                ['name' => 'Point Pleasant Beach', 'state_id' => 3954],
                ['name' => 'Seaside Heights', 'state_id' => 3954],
                ['name' => 'Seaside Park', 'state_id' => 3954],
                ['name' => 'Toms River', 'state_id' => 3954],
                ['name' => 'Waretown', 'state_id' => 3954],
                ['name' => 'Manchester Township', 'state_id' => 3954],
                ['name' => 'Clifton', 'state_id' => 3954],
                ['name' => 'Passaic', 'state_id' => 3954],
                ['name' => 'Bloomingdale', 'state_id' => 3954],
                ['name' => 'Haskell', 'state_id' => 3954],
                ['name' => 'Hewitt', 'state_id' => 3954],
                ['name' => 'Little Falls', 'state_id' => 3954],
                ['name' => 'Newfoundland', 'state_id' => 3954],
                ['name' => 'Oak Ridge', 'state_id' => 3954],
                ['name' => 'Pompton Lakes', 'state_id' => 3954],
                ['name' => 'Ringwood', 'state_id' => 3954],
                ['name' => 'Wanaque', 'state_id' => 3954],
                ['name' => 'Wayne', 'state_id' => 3954],
                ['name' => 'West Milford', 'state_id' => 3954],
                ['name' => 'Paterson', 'state_id' => 3954],
                ['name' => 'Hawthorne', 'state_id' => 3954],
                ['name' => 'Haledon', 'state_id' => 3954],
                ['name' => 'Totowa', 'state_id' => 3954],
                ['name' => 'Alloway', 'state_id' => 3954],
                ['name' => 'Deepwater', 'state_id' => 3954],
                ['name' => 'Hancocks Bridge', 'state_id' => 3954],
                ['name' => 'Pedricktown', 'state_id' => 3954],
                ['name' => 'Penns Grove', 'state_id' => 3954],
                ['name' => 'Pennsville', 'state_id' => 3954],
                ['name' => 'Quinton', 'state_id' => 3954],
                ['name' => 'Salem', 'state_id' => 3954],
                ['name' => 'Woodstown', 'state_id' => 3954],
                ['name' => 'Elmer', 'state_id' => 3954],
                ['name' => 'Norma', 'state_id' => 3954],
                ['name' => 'Warren', 'state_id' => 3954],
                ['name' => 'Basking Ridge', 'state_id' => 3954],
                ['name' => 'Bedminster', 'state_id' => 3954],
                ['name' => 'Bernardsville', 'state_id' => 3954],
                ['name' => 'Far Hills', 'state_id' => 3954],
                ['name' => 'Gladstone', 'state_id' => 3954],
                ['name' => 'Liberty Corner', 'state_id' => 3954],
                ['name' => 'Lyons', 'state_id' => 3954],
                ['name' => 'Peapack', 'state_id' => 3954],
                ['name' => 'Pluckemin', 'state_id' => 3954],
                ['name' => 'Belle Mead', 'state_id' => 3954],
                ['name' => 'Blawenburg', 'state_id' => 3954],
                ['name' => 'Kingston', 'state_id' => 3954],
                ['name' => 'Rocky Hill', 'state_id' => 3954],
                ['name' => 'Skillman', 'state_id' => 3954],
                ['name' => 'Bound Brook', 'state_id' => 3954],
                ['name' => 'Bridgewater', 'state_id' => 3954],
                ['name' => 'Flagtown', 'state_id' => 3954],
                ['name' => 'Franklin Park', 'state_id' => 3954],
                ['name' => 'Manville', 'state_id' => 3954],
                ['name' => 'Martinsville', 'state_id' => 3954],
                ['name' => 'Hillsborough', 'state_id' => 3954],
                ['name' => 'Neshanic Station', 'state_id' => 3954],
                ['name' => 'Raritan', 'state_id' => 3954],
                ['name' => 'Somerset', 'state_id' => 3954],
                ['name' => 'Somerville', 'state_id' => 3954],
                ['name' => 'South Bound Brook', 'state_id' => 3954],
                ['name' => 'Zarephath', 'state_id' => 3954],
                ['name' => 'Franklin', 'state_id' => 3954],
                ['name' => 'Glenwood', 'state_id' => 3954],
                ['name' => 'Hamburg', 'state_id' => 3954],
                ['name' => 'Highland Lakes', 'state_id' => 3954],
                ['name' => 'Mc Afee', 'state_id' => 3954],
                ['name' => 'Ogdensburg', 'state_id' => 3954],
                ['name' => 'Stockholm', 'state_id' => 3954],
                ['name' => 'Sussex', 'state_id' => 3954],
                ['name' => 'Vernon', 'state_id' => 3954],
                ['name' => 'Andover', 'state_id' => 3954],
                ['name' => 'Augusta', 'state_id' => 3954],
                ['name' => 'Branchville', 'state_id' => 3954],
                ['name' => 'Montague', 'state_id' => 3954],
                ['name' => 'Glasser', 'state_id' => 3954],
                ['name' => 'Greendell', 'state_id' => 3954],
                ['name' => 'Hopatcong', 'state_id' => 3954],
                ['name' => 'Lafayette', 'state_id' => 3954],
                ['name' => 'Layton', 'state_id' => 3954],
                ['name' => 'Middleville', 'state_id' => 3954],
                ['name' => 'Newton', 'state_id' => 3954],
                ['name' => 'Sparta', 'state_id' => 3954],
                ['name' => 'Stanhope', 'state_id' => 3954],
                ['name' => 'Stillwater', 'state_id' => 3954],
                ['name' => 'Swartswood', 'state_id' => 3954],
                ['name' => 'Tranquility', 'state_id' => 3954],
                ['name' => 'Wallpack Center', 'state_id' => 3954],
                ['name' => 'Cranford', 'state_id' => 3954],
                ['name' => 'Fanwood', 'state_id' => 3954],
                ['name' => 'Garwood', 'state_id' => 3954],
                ['name' => 'Kenilworth', 'state_id' => 3954],
                ['name' => 'Linden', 'state_id' => 3954],
                ['name' => 'Plainfield', 'state_id' => 3954],
                ['name' => 'Rahway', 'state_id' => 3954],
                ['name' => 'Clark', 'state_id' => 3954],
                ['name' => 'Watchung', 'state_id' => 3954],
                ['name' => 'Scotch Plains', 'state_id' => 3954],
                ['name' => 'Springfield', 'state_id' => 3954],
                ['name' => 'Union', 'state_id' => 3954],
                ['name' => 'Vauxhall', 'state_id' => 3954],
                ['name' => 'Westfield', 'state_id' => 3954],
                ['name' => 'Mountainside', 'state_id' => 3954],
                ['name' => 'Elizabeth', 'state_id' => 3954],
                ['name' => 'Roselle', 'state_id' => 3954],
                ['name' => 'Roselle Park', 'state_id' => 3954],
                ['name' => 'Hillside', 'state_id' => 3954],
                ['name' => 'Elizabethport', 'state_id' => 3954],
                ['name' => 'Summit', 'state_id' => 3954],
                ['name' => 'Berkeley Heights', 'state_id' => 3954],
                ['name' => 'New Providence', 'state_id' => 3954],
                ['name' => 'Allamuchy', 'state_id' => 3954],
                ['name' => 'Belvidere', 'state_id' => 3954],
                ['name' => 'Blairstown', 'state_id' => 3954],
                ['name' => 'Buttzville', 'state_id' => 3954],
                ['name' => 'Changewater', 'state_id' => 3954],
                ['name' => 'Columbia', 'state_id' => 3954],
                ['name' => 'Delaware', 'state_id' => 3954],
                ['name' => 'Great Meadows', 'state_id' => 3954],
                ['name' => 'Hackettstown', 'state_id' => 3954],
                ['name' => 'Hope', 'state_id' => 3954],
                ['name' => 'Johnsonburg', 'state_id' => 3954],
                ['name' => 'Oxford', 'state_id' => 3954],
                ['name' => 'Port Murray', 'state_id' => 3954],
                ['name' => 'Vienna', 'state_id' => 3954],
                ['name' => 'Washington', 'state_id' => 3954],
                ['name' => 'Asbury', 'state_id' => 3954],
                ['name' => 'Broadway', 'state_id' => 3954],
                ['name' => 'Phillipsburg', 'state_id' => 3954],
                ['name' => 'Stewartsville', 'state_id' => 3954]
            ]);
        }
    }
}
