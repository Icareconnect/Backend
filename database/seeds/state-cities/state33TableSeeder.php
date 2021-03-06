<?php

use Illuminate\Database\Seeder;

class state33TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Create Cities for the state of NM - New Mexico.
        //If the table 'cities' exists, insert the data to the table.
        if (DB::table('cities')->get()->count() >= 0) {
            DB::table('cities')->insert([
                ['name' => 'Cedar Crest', 'state_id' => 3956],
                ['name' => 'Isleta', 'state_id' => 3956],
                ['name' => 'Sandia Park', 'state_id' => 3956],
                ['name' => 'Tijeras', 'state_id' => 3956],
                ['name' => 'Albuquerque', 'state_id' => 3956],
                ['name' => 'Kirtland Afb', 'state_id' => 3956],
                ['name' => 'Rio Rancho', 'state_id' => 3956],
                ['name' => 'Aragon', 'state_id' => 3956],
                ['name' => 'Datil', 'state_id' => 3956],
                ['name' => 'Luna', 'state_id' => 3956],
                ['name' => 'Pie Town', 'state_id' => 3956],
                ['name' => 'Quemado', 'state_id' => 3956],
                ['name' => 'Reserve', 'state_id' => 3956],
                ['name' => 'Glenwood', 'state_id' => 3956],
                ['name' => 'Roswell', 'state_id' => 3956],
                ['name' => 'Dexter', 'state_id' => 3956],
                ['name' => 'Hagerman', 'state_id' => 3956],
                ['name' => 'Lake Arthur', 'state_id' => 3956],
                ['name' => 'Bluewater', 'state_id' => 3956],
                ['name' => 'Casa Blanca', 'state_id' => 3956],
                ['name' => 'Cubero', 'state_id' => 3956],
                ['name' => 'Grants', 'state_id' => 3956],
                ['name' => 'Milan', 'state_id' => 3956],
                ['name' => 'Laguna', 'state_id' => 3956],
                ['name' => 'New Laguna', 'state_id' => 3956],
                ['name' => 'Paguate', 'state_id' => 3956],
                ['name' => 'San Fidel', 'state_id' => 3956],
                ['name' => 'San Rafael', 'state_id' => 3956],
                ['name' => 'Fence Lake', 'state_id' => 3956],
                ['name' => 'Pinehill', 'state_id' => 3956],
                ['name' => 'Angel Fire', 'state_id' => 3956],
                ['name' => 'Cimarron', 'state_id' => 3956],
                ['name' => 'Eagle Nest', 'state_id' => 3956],
                ['name' => 'Maxwell', 'state_id' => 3956],
                ['name' => 'Miami', 'state_id' => 3956],
                ['name' => 'Raton', 'state_id' => 3956],
                ['name' => 'Springer', 'state_id' => 3956],
                ['name' => 'Ute Park', 'state_id' => 3956],
                ['name' => 'Clovis', 'state_id' => 3956],
                ['name' => 'Cannon Afb', 'state_id' => 3956],
                ['name' => 'Broadview', 'state_id' => 3956],
                ['name' => 'Grady', 'state_id' => 3956],
                ['name' => 'Melrose', 'state_id' => 3956],
                ['name' => 'Saint Vrain', 'state_id' => 3956],
                ['name' => 'Texico', 'state_id' => 3956],
                ['name' => 'Fort Sumner', 'state_id' => 3956],
                ['name' => 'Taiban', 'state_id' => 3956],
                ['name' => 'Yeso', 'state_id' => 3956],
                ['name' => 'Garfield', 'state_id' => 3956],
                ['name' => 'Hatch', 'state_id' => 3956],
                ['name' => 'Rincon', 'state_id' => 3956],
                ['name' => 'Salem', 'state_id' => 3956],
                ['name' => 'Las Cruces', 'state_id' => 3956],
                ['name' => 'White Sands Missile Range', 'state_id' => 3956],
                ['name' => 'Santa Teresa', 'state_id' => 3956],
                ['name' => 'Anthony', 'state_id' => 3956],
                ['name' => 'Berino', 'state_id' => 3956],
                ['name' => 'Chamberino', 'state_id' => 3956],
                ['name' => 'Dona Ana', 'state_id' => 3956],
                ['name' => 'Fairacres', 'state_id' => 3956],
                ['name' => 'La Mesa', 'state_id' => 3956],
                ['name' => 'Mesilla', 'state_id' => 3956],
                ['name' => 'Mesilla Park', 'state_id' => 3956],
                ['name' => 'Mesquite', 'state_id' => 3956],
                ['name' => 'Organ', 'state_id' => 3956],
                ['name' => 'Radium Springs', 'state_id' => 3956],
                ['name' => 'San Miguel', 'state_id' => 3956],
                ['name' => 'Sunland Park', 'state_id' => 3956],
                ['name' => 'Vado', 'state_id' => 3956],
                ['name' => 'Artesia', 'state_id' => 3956],
                ['name' => 'Carlsbad', 'state_id' => 3956],
                ['name' => 'Hope', 'state_id' => 3956],
                ['name' => 'Lakewood', 'state_id' => 3956],
                ['name' => 'Loco Hills', 'state_id' => 3956],
                ['name' => 'Loving', 'state_id' => 3956],
                ['name' => 'Malaga', 'state_id' => 3956],
                ['name' => 'Whites City', 'state_id' => 3956],
                ['name' => 'Arenas Valley', 'state_id' => 3956],
                ['name' => 'Bayard', 'state_id' => 3956],
                ['name' => 'Buckhorn', 'state_id' => 3956],
                ['name' => 'Santa Clara', 'state_id' => 3956],
                ['name' => 'Cliff', 'state_id' => 3956],
                ['name' => 'Faywood', 'state_id' => 3956],
                ['name' => 'Fort Bayard', 'state_id' => 3956],
                ['name' => 'Gila', 'state_id' => 3956],
                ['name' => 'Hachita', 'state_id' => 3956],
                ['name' => 'Hanover', 'state_id' => 3956],
                ['name' => 'Hurley', 'state_id' => 3956],
                ['name' => 'Mimbres', 'state_id' => 3956],
                ['name' => 'Mule Creek', 'state_id' => 3956],
                ['name' => 'Pinos Altos', 'state_id' => 3956],
                ['name' => 'Redrock', 'state_id' => 3956],
                ['name' => 'Silver City', 'state_id' => 3956],
                ['name' => 'Tyrone', 'state_id' => 3956],
                ['name' => 'Anton Chico', 'state_id' => 3956],
                ['name' => 'La Loma', 'state_id' => 3956],
                ['name' => 'Vaughn', 'state_id' => 3956],
                ['name' => 'Cuervo', 'state_id' => 3956],
                ['name' => 'Newkirk', 'state_id' => 3956],
                ['name' => 'Santa Rosa', 'state_id' => 3956],
                ['name' => 'Mills', 'state_id' => 3956],
                ['name' => 'Mosquero', 'state_id' => 3956],
                ['name' => 'Roy', 'state_id' => 3956],
                ['name' => 'Solano', 'state_id' => 3956],
                ['name' => 'Playas', 'state_id' => 3956],
                ['name' => 'Animas', 'state_id' => 3956],
                ['name' => 'Lordsburg', 'state_id' => 3956],
                ['name' => 'Rodeo', 'state_id' => 3956],
                ['name' => 'Crossroads', 'state_id' => 3956],
                ['name' => 'Caprock', 'state_id' => 3956],
                ['name' => 'Eunice', 'state_id' => 3956],
                ['name' => 'Hobbs', 'state_id' => 3956],
                ['name' => 'Jal', 'state_id' => 3956],
                ['name' => 'Lovington', 'state_id' => 3956],
                ['name' => 'Mcdonald', 'state_id' => 3956],
                ['name' => 'Maljamar', 'state_id' => 3956],
                ['name' => 'Monument', 'state_id' => 3956],
                ['name' => 'Tatum', 'state_id' => 3956],
                ['name' => 'Carrizozo', 'state_id' => 3956],
                ['name' => 'Alto', 'state_id' => 3956],
                ['name' => 'Capitan', 'state_id' => 3956],
                ['name' => 'Corona', 'state_id' => 3956],
                ['name' => 'Fort Stanton', 'state_id' => 3956],
                ['name' => 'Glencoe', 'state_id' => 3956],
                ['name' => 'Hondo', 'state_id' => 3956],
                ['name' => 'Lincoln', 'state_id' => 3956],
                ['name' => 'Nogal', 'state_id' => 3956],
                ['name' => 'Picacho', 'state_id' => 3956],
                ['name' => 'Ruidoso', 'state_id' => 3956],
                ['name' => 'Ruidoso Downs', 'state_id' => 3956],
                ['name' => 'San Patricio', 'state_id' => 3956],
                ['name' => 'Tinnie', 'state_id' => 3956],
                ['name' => 'Los Alamos', 'state_id' => 3956],
                ['name' => 'White Rock', 'state_id' => 3956],
                ['name' => 'Columbus', 'state_id' => 3956],
                ['name' => 'Deming', 'state_id' => 3956],
                ['name' => 'Prewitt', 'state_id' => 3956],
                ['name' => 'Gallup', 'state_id' => 3956],
                ['name' => 'Brimhall', 'state_id' => 3956],
                ['name' => 'Church Rock', 'state_id' => 3956],
                ['name' => 'Continental Divide', 'state_id' => 3956],
                ['name' => 'Crownpoint', 'state_id' => 3956],
                ['name' => 'Fort Wingate', 'state_id' => 3956],
                ['name' => 'Gamerco', 'state_id' => 3956],
                ['name' => 'Mentmore', 'state_id' => 3956],
                ['name' => 'Mexican Springs', 'state_id' => 3956],
                ['name' => 'Ramah', 'state_id' => 3956],
                ['name' => 'Rehoboth', 'state_id' => 3956],
                ['name' => 'Thoreau', 'state_id' => 3956],
                ['name' => 'Tohatchi', 'state_id' => 3956],
                ['name' => 'Vanderwagen', 'state_id' => 3956],
                ['name' => 'Zuni', 'state_id' => 3956],
                ['name' => 'Navajo', 'state_id' => 3956],
                ['name' => 'Jamestown', 'state_id' => 3956],
                ['name' => 'Smith Lake', 'state_id' => 3956],
                ['name' => 'Yatahey', 'state_id' => 3956],
                ['name' => 'Buena Vista', 'state_id' => 3956],
                ['name' => 'Chacon', 'state_id' => 3956],
                ['name' => 'Cleveland', 'state_id' => 3956],
                ['name' => 'Guadalupita', 'state_id' => 3956],
                ['name' => 'Holman', 'state_id' => 3956],
                ['name' => 'Mora', 'state_id' => 3956],
                ['name' => 'Ocate', 'state_id' => 3956],
                ['name' => 'Ojo Feliz', 'state_id' => 3956],
                ['name' => 'Rainsville', 'state_id' => 3956],
                ['name' => 'Valmora', 'state_id' => 3956],
                ['name' => 'Wagon Mound', 'state_id' => 3956],
                ['name' => 'Watrous', 'state_id' => 3956],
                ['name' => 'Chaparral', 'state_id' => 3956],
                ['name' => 'Alamogordo', 'state_id' => 3956],
                ['name' => 'Bent', 'state_id' => 3956],
                ['name' => 'Cloudcroft', 'state_id' => 3956],
                ['name' => 'High Rolls Mountain Park', 'state_id' => 3956],
                ['name' => 'Holloman Air Force Base', 'state_id' => 3956],
                ['name' => 'La Luz', 'state_id' => 3956],
                ['name' => 'Mayhill', 'state_id' => 3956],
                ['name' => 'Mescalero', 'state_id' => 3956],
                ['name' => 'Orogrande', 'state_id' => 3956],
                ['name' => 'Pinon', 'state_id' => 3956],
                ['name' => 'Sacramento', 'state_id' => 3956],
                ['name' => 'Sunspot', 'state_id' => 3956],
                ['name' => 'Timberon', 'state_id' => 3956],
                ['name' => 'Tularosa', 'state_id' => 3956],
                ['name' => 'Weed', 'state_id' => 3956],
                ['name' => 'House', 'state_id' => 3956],
                ['name' => 'Tucumcari', 'state_id' => 3956],
                ['name' => 'Bard', 'state_id' => 3956],
                ['name' => 'Logan', 'state_id' => 3956],
                ['name' => 'Mcalister', 'state_id' => 3956],
                ['name' => 'Nara Visa', 'state_id' => 3956],
                ['name' => 'Quay', 'state_id' => 3956],
                ['name' => 'San Jon', 'state_id' => 3956],
                ['name' => 'Coyote', 'state_id' => 3956],
                ['name' => 'Gallina', 'state_id' => 3956],
                ['name' => 'Lindrith', 'state_id' => 3956],
                ['name' => 'Youngsville', 'state_id' => 3956],
                ['name' => 'Abiquiu', 'state_id' => 3956],
                ['name' => 'Alcalde', 'state_id' => 3956],
                ['name' => 'Canjilon', 'state_id' => 3956],
                ['name' => 'Canones', 'state_id' => 3956],
                ['name' => 'Cebolla', 'state_id' => 3956],
                ['name' => 'Chama', 'state_id' => 3956],
                ['name' => 'Chimayo', 'state_id' => 3956],
                ['name' => 'Cordova', 'state_id' => 3956],
                ['name' => 'Dixon', 'state_id' => 3956],
                ['name' => 'Dulce', 'state_id' => 3956],
                ['name' => 'El Rito', 'state_id' => 3956],
                ['name' => 'Embudo', 'state_id' => 3956],
                ['name' => 'Espanola', 'state_id' => 3956],
                ['name' => 'Hernandez', 'state_id' => 3956],
                ['name' => 'La Madera', 'state_id' => 3956],
                ['name' => 'Medanales', 'state_id' => 3956],
                ['name' => 'Los Ojos', 'state_id' => 3956],
                ['name' => 'Petaca', 'state_id' => 3956],
                ['name' => 'Ohkay Owingeh', 'state_id' => 3956],
                ['name' => 'Tierra Amarilla', 'state_id' => 3956],
                ['name' => 'Truchas', 'state_id' => 3956],
                ['name' => 'Vallecitos', 'state_id' => 3956],
                ['name' => 'Velarde', 'state_id' => 3956],
                ['name' => 'Causey', 'state_id' => 3956],
                ['name' => 'Dora', 'state_id' => 3956],
                ['name' => 'Elida', 'state_id' => 3956],
                ['name' => 'Floyd', 'state_id' => 3956],
                ['name' => 'Kenna', 'state_id' => 3956],
                ['name' => 'Lingo', 'state_id' => 3956],
                ['name' => 'Milnesand', 'state_id' => 3956],
                ['name' => 'Pep', 'state_id' => 3956],
                ['name' => 'Portales', 'state_id' => 3956],
                ['name' => 'Rogers', 'state_id' => 3956],
                ['name' => 'Algodones', 'state_id' => 3956],
                ['name' => 'Bernalillo', 'state_id' => 3956],
                ['name' => 'Cuba', 'state_id' => 3956],
                ['name' => 'Counselor', 'state_id' => 3956],
                ['name' => 'Jemez Pueblo', 'state_id' => 3956],
                ['name' => 'Jemez Springs', 'state_id' => 3956],
                ['name' => 'La Jara', 'state_id' => 3956],
                ['name' => 'Pena Blanca', 'state_id' => 3956],
                ['name' => 'Placitas', 'state_id' => 3956],
                ['name' => 'Ponderosa', 'state_id' => 3956],
                ['name' => 'Regina', 'state_id' => 3956],
                ['name' => 'Corrales', 'state_id' => 3956],
                ['name' => 'Santo Domingo Pueblo', 'state_id' => 3956],
                ['name' => 'San Ysidro', 'state_id' => 3956],
                ['name' => 'Cochiti Pueblo', 'state_id' => 3956],
                ['name' => 'Cochiti Lake', 'state_id' => 3956],
                ['name' => 'Nageezi', 'state_id' => 3956],
                ['name' => 'Sheep Springs', 'state_id' => 3956],
                ['name' => 'Farmington', 'state_id' => 3956],
                ['name' => 'Aztec', 'state_id' => 3956],
                ['name' => 'Blanco', 'state_id' => 3956],
                ['name' => 'Bloomfield', 'state_id' => 3956],
                ['name' => 'Flora Vista', 'state_id' => 3956],
                ['name' => 'Fruitland', 'state_id' => 3956],
                ['name' => 'Kirtland', 'state_id' => 3956],
                ['name' => 'La Plata', 'state_id' => 3956],
                ['name' => 'Navajo Dam', 'state_id' => 3956],
                ['name' => 'Shiprock', 'state_id' => 3956],
                ['name' => 'Waterflow', 'state_id' => 3956],
                ['name' => 'Newcomb', 'state_id' => 3956],
                ['name' => 'Sanostee', 'state_id' => 3956],
                ['name' => 'Ilfeld', 'state_id' => 3956],
                ['name' => 'Pecos', 'state_id' => 3956],
                ['name' => 'Ribera', 'state_id' => 3956],
                ['name' => 'Rowe', 'state_id' => 3956],
                ['name' => 'San Jose', 'state_id' => 3956],
                ['name' => 'Serafina', 'state_id' => 3956],
                ['name' => 'Tererro', 'state_id' => 3956],
                ['name' => 'Villanueva', 'state_id' => 3956],
                ['name' => 'Las Vegas', 'state_id' => 3956],
                ['name' => 'Montezuma', 'state_id' => 3956],
                ['name' => 'Rociada', 'state_id' => 3956],
                ['name' => 'Sapello', 'state_id' => 3956],
                ['name' => 'Conchas Dam', 'state_id' => 3956],
                ['name' => 'Garita', 'state_id' => 3956],
                ['name' => 'Trementina', 'state_id' => 3956],
                ['name' => 'Cerrillos', 'state_id' => 3956],
                ['name' => 'Edgewood', 'state_id' => 3956],
                ['name' => 'Stanley', 'state_id' => 3956],
                ['name' => 'Santa Fe', 'state_id' => 3956],
                ['name' => 'Glorieta', 'state_id' => 3956],
                ['name' => 'Lamy', 'state_id' => 3956],
                ['name' => 'Santa Cruz', 'state_id' => 3956],
                ['name' => 'Tesuque', 'state_id' => 3956],
                ['name' => 'Spaceport City', 'state_id' => 3956],
                ['name' => 'Truth Or Consequences', 'state_id' => 3956],
                ['name' => 'Arrey', 'state_id' => 3956],
                ['name' => 'Caballo', 'state_id' => 3956],
                ['name' => 'Derry', 'state_id' => 3956],
                ['name' => 'Elephant Butte', 'state_id' => 3956],
                ['name' => 'Monticello', 'state_id' => 3956],
                ['name' => 'Williamsburg', 'state_id' => 3956],
                ['name' => 'Winston', 'state_id' => 3956],
                ['name' => 'Hillsboro', 'state_id' => 3956],
                ['name' => 'Claunch', 'state_id' => 3956],
                ['name' => 'La Joya', 'state_id' => 3956],
                ['name' => 'Veguita', 'state_id' => 3956],
                ['name' => 'Socorro', 'state_id' => 3956],
                ['name' => 'Lemitar', 'state_id' => 3956],
                ['name' => 'Magdalena', 'state_id' => 3956],
                ['name' => 'Polvadera', 'state_id' => 3956],
                ['name' => 'San Acacia', 'state_id' => 3956],
                ['name' => 'San Antonio', 'state_id' => 3956],
                ['name' => 'Amalia', 'state_id' => 3956],
                ['name' => 'Arroyo Hondo', 'state_id' => 3956],
                ['name' => 'Arroyo Seco', 'state_id' => 3956],
                ['name' => 'Carson', 'state_id' => 3956],
                ['name' => 'Cerro', 'state_id' => 3956],
                ['name' => 'Chamisal', 'state_id' => 3956],
                ['name' => 'Costilla', 'state_id' => 3956],
                ['name' => 'Taos Ski Valley', 'state_id' => 3956],
                ['name' => 'El Prado', 'state_id' => 3956],
                ['name' => 'Llano', 'state_id' => 3956],
                ['name' => 'Ojo Caliente', 'state_id' => 3956],
                ['name' => 'Penasco', 'state_id' => 3956],
                ['name' => 'Questa', 'state_id' => 3956],
                ['name' => 'Ranchos De Taos', 'state_id' => 3956],
                ['name' => 'Red River', 'state_id' => 3956],
                ['name' => 'San Cristobal', 'state_id' => 3956],
                ['name' => 'Taos', 'state_id' => 3956],
                ['name' => 'Trampas', 'state_id' => 3956],
                ['name' => 'Tres Piedras', 'state_id' => 3956],
                ['name' => 'Vadito', 'state_id' => 3956],
                ['name' => 'Valdez', 'state_id' => 3956],
                ['name' => 'Cedarvale', 'state_id' => 3956],
                ['name' => 'Estancia', 'state_id' => 3956],
                ['name' => 'Mcintosh', 'state_id' => 3956],
                ['name' => 'Moriarty', 'state_id' => 3956],
                ['name' => 'Mountainair', 'state_id' => 3956],
                ['name' => 'Torreon', 'state_id' => 3956],
                ['name' => 'Willard', 'state_id' => 3956],
                ['name' => 'Clines Corners', 'state_id' => 3956],
                ['name' => 'Encino', 'state_id' => 3956],
                ['name' => 'Amistad', 'state_id' => 3956],
                ['name' => 'Capulin', 'state_id' => 3956],
                ['name' => 'Clayton', 'state_id' => 3956],
                ['name' => 'Des Moines', 'state_id' => 3956],
                ['name' => 'Folsom', 'state_id' => 3956],
                ['name' => 'Gladstone', 'state_id' => 3956],
                ['name' => 'Grenville', 'state_id' => 3956],
                ['name' => 'Sedan', 'state_id' => 3956],
                ['name' => 'Belen', 'state_id' => 3956],
                ['name' => 'Bosque', 'state_id' => 3956],
                ['name' => 'Jarales', 'state_id' => 3956],
                ['name' => 'Los Lunas', 'state_id' => 3956],
                ['name' => 'Pueblo Of Acoma', 'state_id' => 3956],
                ['name' => 'Peralta', 'state_id' => 3956],
                ['name' => 'Tome', 'state_id' => 3956],
                ['name' => 'Bosque Farms', 'state_id' => 3956]
            ]);
        }
    }
}
