-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 03, 2025 at 04:59 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `expensevoyage`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$Do4q2j0WJrDclkecD3owvOmeum0idpSUsLicQTHFgeDTKGSVS2VfG');

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `short_desc` text NOT NULL,
  `category` varchar(100) NOT NULL,
  `author` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `reading_time` varchar(20) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `title`, `short_desc`, `category`, `author`, `date`, `tags`, `reading_time`, `location`, `rating`, `image`) VALUES
(4, 'Top 10 Things to Do in Bali', 'Discover must-visit spots, food, and experiences in Bali.', 'Trip Guides', 'Sarah Mitchell', '2025-08-01', 'Bali, Travel, Beaches, Culture', '8 min', 'Bali, Indonesia', 4.8, 'images/blog1.jpg'),
(5, 'A Complete Guide to the Swiss Alps', 'From scenic trails to cozy chalets, plan your perfect Alps trip.', 'Trip Guides', 'Daniel Carter', '2025-08-02', 'Switzerland, Alps, Hiking, Skiing', '10 min', 'Swiss Alps, Switzerland', 4.9, 'images/blog2.jpg'),
(6, 'Exploring Dubai on a Budget', 'Luxury Dubai can be affordable. Here’s how.', 'Trip Guides', 'Emily Thompson', '2025-08-03', 'Dubai, Budget Travel, Middle East', '7 min', 'Dubai, UAE', 4.7, 'images/blog3.jpg'),
(7, 'Hidden Gems of Thailand', 'Offbeat spots in Thailand you should not miss.', 'Trip Guides', 'James Walker', '2025-08-04', 'Thailand, Hidden Gems, Asia Travel', '9 min', 'Thailand', 4.8, 'images/blog4.jpg'),
(8, '5 Budget Travel Hacks for 2025', 'Save money and travel more.', 'Budget Tips', 'Olivia Bennett', '2025-08-05', 'Budget Travel, Tips, Saving Money', '6 min', 'Worldwide', 4.6, 'images/blog5.jpg'),
(9, 'How to Find Cheap Flights', 'Best strategies for scoring low-cost air tickets.', 'Budget Tips', 'Ethan Parker', '2025-08-06', 'Flights, Cheap Tickets, Travel Deals', '5 min', 'Worldwide', 4.7, 'images/blog6.jpg'),
(10, 'Budget-Friendly European Cities', 'Explore Europe without breaking the bank.', 'Budget Tips', 'Sophia Collins', '2025-08-07', 'Europe, Budget Cities, Travel Guide', '8 min', 'Europe', 4.7, 'images/blog7.jpg'),
(11, 'Save Money on Hotels', 'Practical tips for affordable stays.', 'Budget Tips', 'Michael Harris', '2025-08-08', 'Hotels, Budget Stay, Travel Hacks', '6 min', 'Worldwide', 4.6, 'images/blog8.jpg'),
(12, 'Ultimate Packing List for Asia', 'Everything you need for an Asia trip.', 'Packing Checklists', 'Isabella Foster', '2025-08-09', 'Asia, Packing List, Travel Essentials', '5 min', 'Asia', 4.5, 'images/blog9.jpg'),
(13, 'Beach Vacation Packing Checklist', 'Must-have items for a beach holiday.', 'Packing Checklists', 'William Turner', '2025-08-10', 'Beach, Packing Checklist, Travel Gear', '5 min', 'Worldwide Beaches', 4.6, 'images/blog10.jpg'),
(14, 'Winter Travel Essentials', 'Stay warm and comfortable in cold climates.', 'Packing Checklists', 'Ava Richardson', '2025-08-11', 'Winter, Cold Weather, Packing Tips', '6 min', 'Cold Destinations', 4.7, 'images/blog11.jpg'),
(15, 'Tech Gear for Travelers', 'Best gadgets to carry when traveling.', 'Packing Checklists', 'Benjamin Scott', '2025-08-12', 'Technology, Gadgets, Travel Gear', '7 min', 'Worldwide', 4.8, 'images/blog12.jpg'),
(16, 'Top 10 Cities to Visit in 2025', 'Our annual list of must-see cities.', 'Top Destinations', 'Chloe Adams', '2025-08-13', 'Cities, 2025, Travel Guide', '8 min', 'Worldwide', 4.9, 'images/blog13.jpg'),
(17, 'World’s Best Beaches', 'From Maldives to Hawaii, explore the best beaches.', 'Top Destinations', 'Lucas Morgan', '2025-08-14', 'Beaches, Maldives, Hawaii', '7 min', 'Worldwide Beaches', 4.8, 'images/blog14.jpg'),
(18, 'Best National Parks Around the World', 'Nature lovers’ ultimate guide.', 'Top Destinations', 'Grace Campbell', '2025-08-15', 'National Parks, Nature, Hiking', '9min', 'Worldwide', 4.9, 'images/blog15.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `blog_details`
--

CREATE TABLE `blog_details` (
  `id` int(11) NOT NULL,
  `blog_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `gallery_images` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_details`
--

INSERT INTO `blog_details` (`id`, `blog_id`, `content`, `gallery_images`) VALUES
(1, 4, 'Bali is often described as the Island of the Gods, and for good reason. This Indonesian paradise offers a captivating mix of golden beaches, emerald rice terraces, and a deep spiritual heritage. From the moment you arrive, the scent of frangipani flowers and the sight of intricate temple offerings greet you, setting the tone for a magical journey. Whether you are drawn by the surf-friendly waves of Kuta, the serene atmosphere of Ubud, or the dramatic cliffside temples of Uluwatu, Bali’s landscapes and culture weave together in perfect harmony.\n\nBeyond its natural beauty, Bali is a cultural treasure trove. Travelers can witness traditional Balinese dance performances, wander through local art markets, and indulge in spa treatments inspired by centuries-old healing traditions. The island’s culinary scene is equally diverse, offering everything from street-side satay stalls to high-end restaurants with ocean views. Each corner of Bali tells its own story, and every traveler can find a rhythm that matches their pace.\n\nFor those seeking adventure, Bali offers an endless array of experiences — hike to the summit of Mount Batur at sunrise, dive into crystal-clear waters teeming with marine life, or explore hidden waterfalls tucked away in lush jungles. And when the day winds down, the sunsets here are nothing short of legendary, painting the sky in hues of gold, pink, and violet as the waves gently lap the shore. Bali is not just a place to visit; it’s a place to feel alive.', 'images/bali1.jpg,images/bali2.jpg,images/bali3.jpg'),
(2, 5, 'The Swiss Alps are a breathtaking destination that offers a perfect balance of adventure and relaxation. From snow-capped peaks to lush alpine meadows, this region attracts travelers year-round. Whether you’re skiing down world-famous slopes in winter or hiking through scenic trails in summer, the Alps deliver unforgettable experiences. Quaint mountain villages like Zermatt and Grindelwald provide charming stays, complete with cozy chalets and local Swiss delicacies.\r\n\r\nOne of the best ways to explore the Alps is by train, with panoramic routes like the Glacier Express offering jaw-dropping views of valleys, lakes, and towering mountains. Beyond the outdoors, you can soak in the cultural richness of Swiss towns, enjoying museums, markets, and traditional alpine festivals. For an all-rounded trip, combine days of adventure with moments of peace—perhaps a spa day overlooking the snow or an evening by the fireplace with a warm fondue.\r\n\r\nNo matter the season, the Swiss Alps leave an indelible mark on travelers, blending natural grandeur with authentic cultural experiences. It’s a destination that captures both the thrill-seeker’s heart and the soul of those in search of serenity.', 'images/alps1.jpg,images/alps2.jpg,images/alps3.jpg'),
(3, 6, 'Dubai is a city that blends modern luxury with deep-rooted traditions, making it a destination like no other. Famous for its futuristic skyline, towering skyscrapers, and golden deserts, Dubai offers experiences that cater to every type of traveler. From the shimmering waters of the Dubai Marina to the bustling souks filled with spices and gold, each corner of the city tells a unique story. The desert safari, camel rides, and traditional Bedouin-style dinners are just as memorable as its world-class shopping malls and attractions.\r\n\r\nBudget travelers will be pleased to know that Dubai can be enjoyed without breaking the bank. By using public transport, dining at local eateries, and exploring free attractions like Jumeirah Beach or the historic Al Fahidi district, visitors can experience the essence of the city affordably. The metro system is clean, efficient, and covers most key spots, making it easy to navigate.\r\n\r\nWhether you’re marveling at the Burj Khalifa’s views, enjoying a dhow cruise, or exploring vibrant neighborhoods, Dubai is a city where tradition and innovation meet beautifully. Its warmth lies not just in its climate, but also in the hospitality of its people.', 'images/dubai1.jpg,images/dubai2.jpg,images/dubai3.jpg'),
(4, 7, 'Thailand is a country of endless discovery, offering everything from tropical beaches to bustling cities and tranquil villages. While popular destinations like Bangkok and Phuket draw millions, Thailand’s hidden gems provide a deeper, more intimate experience. Imagine quiet stretches of sand on Koh Lanta, scenic mountain roads in Mae Hong Son, and charming towns where locals greet you with warm smiles.\r\n\r\nThese lesser-known spots allow travelers to immerse themselves in authentic Thai culture. Visit morning markets filled with fresh produce, savor street food like som tam and mango sticky rice, and witness traditional ceremonies in rural temples. Life moves at a slower pace here, giving you a chance to connect with the land and its people.\r\n\r\nAdventure seekers can explore limestone caves, kayak through mangrove forests, or hike in lush jungles. Whether it’s the gentle sway of a hammock by the sea or the thrill of discovering a hidden waterfall, Thailand’s beauty lies in the moments you least expect.', 'images/thai1.jpg,images/thai2.jpg,images/thai3.jpg'),
(5, 8, 'Traveling on a budget does not mean compromising on experiences—it means making smarter choices. By carefully planning your itinerary, seeking out discounts, and embracing flexibility, you can stretch your travel budget further than you imagined. From booking flights during off-peak seasons to choosing affordable accommodations like hostels or guesthouses, there are countless ways to save.\r\n\r\nFood is another area where budget travelers can cut costs without sacrificing flavor. Eating at local markets not only saves money but also gives you a taste of authentic cuisine. Many cities also offer free walking tours, public parks, and cultural events, allowing you to enjoy rich experiences without spending a dime.\r\n\r\nTraveling affordably is about prioritizing what matters most to you. Whether it’s exploring one city deeply or visiting multiple destinations at a slower pace, a thoughtful budget ensures every trip is both memorable and sustainable.', 'images/budget1.jpg,images/budget2.jpg'),
(6, 9, 'Finding cheap flights is often the first step in making travel dreams a reality. With the right strategies, you can save hundreds on airfare and use that money for experiences at your destination. Booking flights midweek, using fare comparison tools, and setting price alerts are simple yet effective ways to find deals. Flying into alternate airports or being open to connecting flights can also reduce costs.\r\n\r\nAnother trick is to travel during shoulder seasons when demand is lower, which often leads to better prices. Consider using airline miles or credit card rewards to offset ticket costs. Many budget-savvy travelers also search for tickets in incognito mode to avoid price hikes based on browsing history.\r\n\r\nUltimately, patience and flexibility are your best allies when hunting for cheap flights. With a little research and timing, you can turn your travel plans from a dream into reality without overspending.', 'images/flight1.jpg,images/flight2.jpg'),
(7, 10, 'Europe on a budget is not only possible—it can be deeply rewarding. The continent offers countless affordable destinations where culture, history, and charm abound. Cities like Budapest, Prague, and Lisbon deliver rich experiences without the heavy price tag of more famous capitals. By walking instead of using taxis and staying in hostels or budget hotels, you can save money while soaking in the local atmosphere.\r\n\r\nPublic transportation networks are efficient and well-connected, making it easy to hop from one city to another. Many museums offer free entry on certain days, and local markets provide inexpensive yet delicious meals. Traveling off-season also means fewer crowds and better deals.\r\n\r\nWith a little planning and an adventurous spirit, you can explore Europe’s stunning architecture, vibrant culture, and natural beauty without draining your wallet. It’s proof that unforgettable travel does not have to be expensive.', 'images/europe1.jpg,images/europe2.jpg'),
(8, 11, 'Hotels are often one of the biggest expenses when traveling, but with the right approach, you can find comfortable stays at a fraction of the cost. Booking early is one of the simplest ways to secure good rates, as prices tend to rise closer to the travel date. Using cashback sites or loyalty programs can also add up to significant savings over time.\r\n\r\nFlexibility in location can help as well—staying slightly outside the city center often means better prices, and with public transport, you can still reach major attractions easily. Last-minute deals can also work in your favor if your travel dates are open.\r\n\r\nBy combining these strategies, you can enjoy cozy stays that fit your budget without sacrificing quality. After all, a good night’s rest is essential to make the most of your adventures.', 'images/hotel1.jpg,images/hotel2.jpg'),
(9, 12, 'Packing smart is key to a smooth and stress-free trip, especially when traveling across diverse climates like those found in Asia. Lightweight clothing that can be layered is essential, along with comfortable walking shoes and a travel adapter. Keeping your luggage minimal allows for easier movement and fewer airline fees.\r\n\r\nConsider packing a small first-aid kit, reusable water bottle, and compact rain gear. Rolling your clothes instead of folding can save space and keep items wrinkle-free. If you plan to visit multiple countries, research cultural norms to pack appropriate attire.\r\n\r\nA well-thought-out packing list ensures you are ready for every adventure without being weighed down. Travel light, but travel prepared—it’s the perfect balance for a great trip.', 'images/asia1.jpg,images/asia2.jpg'),
(10, 13, 'Beach vacations are all about relaxation, sunshine, and fun. Packing the right items ensures you can make the most of your time by the sea. Essentials like sunscreen, sunglasses, and a wide-brimmed hat protect you from the sun, while flip-flops and light beachwear keep you comfortable.\r\n\r\nFor activities, bring snorkeling gear, a waterproof phone case, and perhaps a good book to enjoy by the shore. A lightweight, waterproof bag helps keep your belongings safe from sand and splashes.\r\n\r\nBeing well-prepared means you can focus on enjoying the sound of the waves, the warmth of the sun, and the beauty of the ocean without any worries.', 'images/beach1.jpg,images/beach2.jpg'),
(11, 14, 'Winter travel requires careful packing to ensure warmth and comfort. Layering is key—start with thermal wear, add sweaters, and top with a waterproof coat. Accessories like gloves, scarves, and hats help retain body heat, while sturdy, insulated boots keep your feet dry in snow.\r\n\r\nDon’t forget to pack skincare essentials like moisturizer and lip balm to protect against the cold, dry air. A reusable thermal flask for hot drinks can make outdoor adventures more enjoyable.\r\n\r\nWith the right gear, winter trips become magical experiences, filled with snowy landscapes, cozy evenings, and the crisp, refreshing air of the season.', 'images/winter1.jpg,images/winter2.jpg'),
(12, 15, 'Travel gadgets can make your journey smoother, safer, and more enjoyable. A high-capacity power bank, universal travel adapter, and noise-cancelling headphones are must-haves for most travelers. A compact camera or smartphone with a good lens helps capture memories, while GPS devices or travel apps assist with navigation.\r\n\r\nPacking these gadgets in padded cases keeps them safe, and organizing cables in a travel pouch prevents tangles. Depending on your needs, you might also bring a lightweight laptop or tablet for work or entertainment on the go.\r\n\r\nThe right tech can transform your trip, giving you more time to focus on experiences rather than logistics.', 'images/tech1.jpg,images/tech2.jpg'),
(13, 16, 'City travel offers a vibrant mix of culture, history, and modern attractions. Exploring on foot is often the best way to absorb the atmosphere, whether you’re wandering through historic districts or enjoying bustling markets. Cities like Paris, Tokyo, and New York each have their own unique energy and landmarks that are instantly recognizable.\r\n\r\nPlanning your days with a mix of sightseeing and relaxation helps prevent burnout. Take time to enjoy local cafes, parks, and cultural events. Using public transportation is usually efficient and affordable.\r\n\r\nEvery city has hidden gems waiting to be discovered, from tucked-away restaurants to small art galleries. These unexpected finds often become the most cherished memories of a trip.', 'images/cities1.jpg,images/cities2.jpg'),
(14, 17, 'Beach destinations are perfect for those seeking both relaxation and adventure. From the turquoise waters of the Maldives to the lush beaches of Hawaii, each location offers its own charm. Visiting during the off-peak season not only means better deals but also a more peaceful experience.\r\n\r\nActivities range from water sports like surfing and snorkeling to simply lounging under the sun. Many beach destinations also offer vibrant nightlife and cultural festivals that add to the experience.\r\n\r\nWhether it’s a romantic escape or a family vacation, beach destinations provide the perfect backdrop for unforgettable memories.', 'images/beaches1.jpg,images/beaches2.jpg'),
(15, 18, 'National parks are nature’s way of showcasing the planet’s most stunning landscapes. From Yellowstone’s geysers to Banff’s mountain peaks and Serengeti’s vast savannas, these protected areas are home to diverse wildlife and ecosystems. Exploring them offers a deep connection to the natural world.\r\n\r\nVisitors should be prepared with the right gear—comfortable hiking boots, adequate water, and a respect for local wildlife. Many parks have well-marked trails, campgrounds, and guided tours to enhance the experience.\r\n\r\nSpending time in national parks is a reminder of the beauty worth preserving. They offer both adventure and tranquility, inspiring awe in everyone who visits.', 'images/parks1.jpg,images/parks2.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `trip_id` int(11) DEFAULT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `num_persons` int(11) DEFAULT 1,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `payment_status` enum('unpaid','paid','refunded') DEFAULT 'unpaid',
  `transaction_id` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `trip_id`, `booking_date`, `num_persons`, `status`, `payment_status`, `transaction_id`, `notes`) VALUES
(10, 5, 35, '2025-09-02 14:14:56', 5, 'pending', 'unpaid', 'CASH-20250902-6D7948', 'Guided sightseeing tour of major attractions.'),
(13, 5, 28, '2025-09-02 14:58:06', 3, 'pending', 'paid', 'PAY-20250902-0D6DE4', 'back to my country'),
(14, 5, 46, '2025-09-02 14:58:22', 6, 'pending', 'unpaid', 'CASH-20250902-86E209', 'Guided sightseeing tour of major attractions.'),
(15, 5, 25, '2025-09-02 14:58:37', 2, 'pending', 'unpaid', 'CASH-20250902-F81312', ''),
(16, 5, 50, '2025-09-02 14:58:52', 1, 'cancelled', 'paid', 'PAY-20250902-2F6980', 'back'),
(18, 5, 22, '2025-09-02 15:01:11', 3, 'pending', 'unpaid', 'CASH-20250902-CF267A', ''),
(19, 5, 40, '2025-09-02 15:01:29', 4, 'pending', 'paid', 'PAY-20250902-AB1B0F', 'Relaxation, shopping and leisure activities.');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(2, 'Muhammad ', 'fasih2412@gmail.com', 'Website', 'This is good travel website', '2025-08-14 12:49:35'),
(3, 'jason', 'fasih2412@gmail.com', 'trip', 'i want to know about upcoming trips or event so i will get trip', '2025-08-27 14:56:58');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `category` varchar(50) NOT NULL,
  `expense_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `trip_id`, `user_id`, `amount`, `category`, `expense_date`, `notes`, `image`) VALUES
(10, 28, 5, 400.00, 'Arrival', '2025-09-05', 'Arrival, hotel check-in and local exploration.', 'img/images/maldives_luxury_retreat_1.jpg'),
(11, 28, 5, 340.00, 'Adventure', '2025-09-06', 'Full-day adventure and cultural activities.', 'img/images/maldives_luxury_retreat_2.jpg'),
(12, 28, 5, 400.00, 'Sightseeing', '2025-09-07', 'Guided sightseeing tour of major attractions.', 'img/images/maldives_luxury_retreat_3.jpg'),
(13, 28, 5, 400.00, 'Leisure', '2025-09-08', 'Relaxation, shopping and leisure activities.', 'img/images/maldives_luxury_retreat_4.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `expenses_templates`
--

CREATE TABLE `expenses_templates` (
  `id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `expense_date` text DEFAULT NULL,
  `day` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses_templates`
--

INSERT INTO `expenses_templates` (`id`, `trip_id`, `category`, `amount`, `notes`, `image`, `expense_date`, `day`) VALUES
(22, 22, 'Arrival', 530.00, 'Arrival in Dubai, hotel check-in, and evening visit', 'img/images/dubai-arrival.jpg', '2025-09-10', 1),
(23, 22, 'Adventure', 340.00, 'Morning desert safari with dune bashing, camel ride', 'img/images/desert-safari.jpg', '2025-09-11', 2),
(24, 22, 'Sightseeing', 250.00, 'Visit Burj Khalifa observation deck, Dubai Aquarium', 'img/images/burj-khalifa.jpg', '2025-09-12', 3),
(25, 22, 'Leisure', 530.00, 'Relax at Jumeirah Beach, last-minute shopping, departure', 'img/images/jumeirah-beach.jpg', '2025-09-13', 4),
(26, 23, 'Arrival', 250.00, 'Arrival, hotel check-in and local exploration.', 'img/images/northern_areas_tour_1.jpg', '2025-08-25', 1),
(27, 23, 'Adventure', 400.00, 'Full-day adventure and cultural activities.', 'img/images/northern_areas_tour_2.jpg', '2025-08-26', 2),
(28, 23, 'Sightseeing', 250.00, 'Guided sightseeing tour of major attractions.', 'img/images/northern_areas_tour_3.jpg', '2025-08-27', 3),
(29, 23, 'Leisure', 530.00, 'Relaxation, shopping and leisure activities.', 'img/images/northern_areas_tour_4.jpg', '2025-08-28', 4),
(30, 24, 'Arrival', 340.00, 'Arrival, hotel check-in and local exploration.', 'img/images/turkey_adventure_1.jpg', '2025-10-15', 1),
(31, 24, 'Adventure', 250.00, 'Full-day adventure and cultural activities.', 'img/images/turkey_adventure_2.jpg', '2025-10-16', 2),
(32, 24, 'Sightseeing', 340.00, 'Guided sightseeing tour of major attractions.', 'img/images/turkey_adventure_3.jpg', '2025-10-17', 3),
(33, 24, 'Leisure', 340.00, 'Relaxation, shopping and leisure activities.', 'img/images/turkey_adventure_4.jpg', '2025-10-18', 4),
(34, 25, 'Arrival', 250.00, 'Arrival, hotel check-in and local exploration.', 'img/images/bali_beach_getaway_1.jpg', '2025-11-05', 1),
(35, 25, 'Adventure', 340.00, 'Full-day adventure and cultural activities.', 'img/images/bali_beach_getaway_2.jpg', '2025-11-06', 2),
(36, 25, 'Sightseeing', 340.00, 'Guided sightseeing tour of major attractions.', 'img/images/bali_beach_getaway_3.jpg', '2025-11-07', 3),
(37, 25, 'Leisure', 340.00, 'Relaxation, shopping and leisure activities.', 'img/images/bali_beach_getaway_4.jpg', '2025-11-08', 4),
(38, 26, 'Arrival', 400.00, 'Arrival, hotel check-in and local exploration.', 'img/images/swiss_alps_adventure_1.jpg', '2026-01-15', 1),
(39, 26, 'Adventure', 530.00, 'Full-day adventure and cultural activities.', 'img/images/swiss_alps_adventure_2.jpg', '2026-01-16', 2),
(40, 26, 'Sightseeing', 250.00, 'Guided sightseeing tour of major attractions.', 'img/images/swiss_alps_adventure_3.jpg', '2026-01-17', 3),
(41, 26, 'Leisure', 250.00, 'Relaxation, shopping and leisure activities.', 'img/images/swiss_alps_adventure_4.jpg', '2026-01-18', 4),
(42, 27, 'Arrival', 530.00, 'Arrival, hotel check-in and local exploration.', 'img/images/paris_romantic_escape_1.jpg', '2025-12-10', 1),
(43, 27, 'Adventure', 530.00, 'Full-day adventure and cultural activities.', 'img/images/paris_romantic_escape_2.jpg', '2025-12-11', 2),
(44, 27, 'Sightseeing', 250.00, 'Guided sightseeing tour of major attractions.', 'img/images/paris_romantic_escape_3.jpg', '2025-12-12', 3),
(45, 27, 'Leisure', 530.00, 'Relaxation, shopping and leisure activities.', 'img/images/paris_romantic_escape_4.jpg', '2025-12-13', 4),
(46, 28, 'Arrival', 400.00, 'Arrival, hotel check-in and local exploration.', 'img/images/maldives_luxury_retreat_1.jpg', '2025-09-05', 1),
(47, 28, 'Adventure', 340.00, 'Full-day adventure and cultural activities.', 'img/images/maldives_luxury_retreat_2.jpg', '2025-09-06', 2),
(48, 28, 'Sightseeing', 400.00, 'Guided sightseeing tour of major attractions.', 'img/images/maldives_luxury_retreat_3.jpg', '2025-09-07', 3),
(49, 28, 'Leisure', 400.00, 'Relaxation, shopping and leisure activities.', 'img/images/maldives_luxury_retreat_4.jpg', '2025-09-08', 4),
(50, 29, 'Arrival', 400.00, 'Arrival, hotel check-in and local exploration.', 'img/images/thailand_island_hopping_1.jpg', '2025-10-02', 1),
(51, 29, 'Adventure', 340.00, 'Full-day adventure and cultural activities.', 'img/images/thailand_island_hopping_2.jpg', '2025-10-03', 2),
(52, 29, 'Sightseeing', 400.00, 'Guided sightseeing tour of major attractions.', 'img/images/thailand_island_hopping_3.jpg', '2025-10-04', 3),
(53, 29, 'Leisure', 400.00, 'Relaxation, shopping and leisure activities.', 'img/images/thailand_island_hopping_4.jpg', '2025-10-05', 4),
(54, 30, 'Arrival', 340.00, 'Arrival, hotel check-in and local exploration.', 'img/images/new_york_city_lights_1.jpg', '2026-03-10', 1),
(55, 30, 'Adventure', 400.00, 'Full-day adventure and cultural activities.', 'img/images/new_york_city_lights_2.jpg', '2026-03-11', 2),
(56, 30, 'Sightseeing', 250.00, 'Guided sightseeing tour of major attractions.', 'img/images/new_york_city_lights_3.jpg', '2026-03-12', 3),
(57, 30, 'Leisure', 340.00, 'Relaxation, shopping and leisure activities.', 'img/images/new_york_city_lights_4.jpg', '2026-03-13', 4),
(58, 31, 'Arrival', 530.00, 'Arrival, hotel check-in and local exploration.', 'img/images/rome_historical_tour_1.jpg', '2025-11-18', 1),
(59, 31, 'Adventure', 250.00, 'Full-day adventure and cultural activities.', 'img/images/rome_historical_tour_2.jpg', '2025-11-19', 2),
(60, 31, 'Sightseeing', 250.00, 'Guided sightseeing tour of major attractions.', 'img/images/rome_historical_tour_3.jpg', '2025-11-20', 3),
(61, 31, 'Leisure', 250.00, 'Relaxation, shopping and leisure activities.', 'img/images/rome_historical_tour_4.jpg', '2025-11-21', 4),
(62, 32, 'Arrival', 400.00, 'Arrival, hotel check-in and local exploration.', 'img/images/santorini_sunset_views_1.jpg', '2026-04-05', 1),
(63, 32, 'Adventure', 250.00, 'Full-day adventure and cultural activities.', 'img/images/santorini_sunset_views_2.jpg', '2026-04-06', 2),
(64, 32, 'Sightseeing', 250.00, 'Guided sightseeing tour of major attractions.', 'img/images/santorini_sunset_views_3.jpg', '2026-04-07', 3),
(65, 32, 'Leisure', 400.00, 'Relaxation, shopping and leisure activities.', 'img/images/santorini_sunset_views_4.jpg', '2026-04-08', 4),
(66, 33, 'Arrival', 340.00, 'Arrival, hotel check-in and local exploration.', 'img/images/london_city_break_1.jpg', '2025-10-22', 1),
(67, 33, 'Adventure', 340.00, 'Full-day adventure and cultural activities.', 'img/images/london_city_break_2.jpg', '2025-10-23', 2),
(68, 33, 'Sightseeing', 530.00, 'Guided sightseeing tour of major attractions.', 'img/images/london_city_break_3.jpg', '2025-10-24', 3),
(69, 33, 'Leisure', 400.00, 'Relaxation, shopping and leisure activities.', 'img/images/london_city_break_4.jpg', '2025-10-25', 4),
(70, 34, 'Arrival', 340.00, 'Arrival, hotel check-in and local exploration.', 'img/images/japan_cherry_blossom_tour_1.jpg', '2026-03-25', 1),
(71, 34, 'Adventure', 530.00, 'Full-day adventure and cultural activities.', 'img/images/japan_cherry_blossom_tour_2.jpg', '2026-03-26', 2),
(72, 34, 'Sightseeing', 250.00, 'Guided sightseeing tour of major attractions.', 'img/images/japan_cherry_blossom_tour_3.jpg', '2026-03-27', 3),
(73, 34, 'Leisure', 530.00, 'Relaxation, shopping and leisure activities.', 'img/images/japan_cherry_blossom_tour_4.jpg', '2026-03-28', 4),
(74, 35, 'Arrival', 400.00, 'Arrival, hotel check-in and local exploration.', 'img/images/egypt_pyramids_adventure_1.jpg', '2025-09-15', 1),
(75, 35, 'Adventure', 400.00, 'Full-day adventure and cultural activities.', 'img/images/egypt_pyramids_adventure_2.jpg', '2025-09-16', 2),
(76, 35, 'Sightseeing', 250.00, 'Guided sightseeing tour of major attractions.', 'img/images/egypt_pyramids_adventure_3.jpg', '2025-09-17', 3),
(77, 35, 'Leisure', 250.00, 'Relaxation, shopping and leisure activities.', 'img/images/egypt_pyramids_adventure_4.jpg', '2025-09-18', 4),
(78, 36, 'Arrival', 250.00, 'Arrival, hotel check-in and local exploration.', 'img/images/south_africa_safari_1.jpg', '2025-08-30', 1),
(79, 36, 'Adventure', 250.00, 'Full-day adventure and cultural activities.', 'img/images/south_africa_safari_2.jpg', '2025-08-31', 2),
(80, 36, 'Sightseeing', 530.00, 'Guided sightseeing tour of major attractions.', 'img/images/south_africa_safari_3.jpg', '2025-09-01', 3),
(81, 36, 'Leisure', 530.00, 'Relaxation, shopping and leisure activities.', 'img/images/south_africa_safari_4.jpg', '2025-09-02', 4),
(82, 37, 'Arrival', 250.00, 'Arrival, hotel check-in and local exploration.', 'img/images/barcelona_culture_trip_1.jpg', '2025-11-08', 1),
(83, 37, 'Adventure', 340.00, 'Full-day adventure and cultural activities.', 'img/images/barcelona_culture_trip_2.jpg', '2025-11-09', 2),
(84, 37, 'Sightseeing', 340.00, 'Guided sightseeing tour of major attractions.', 'img/images/barcelona_culture_trip_3.jpg', '2025-11-10', 3),
(85, 37, 'Leisure', 400.00, 'Relaxation, shopping and leisure activities.', 'img/images/barcelona_culture_trip_4.jpg', '2025-11-11', 4),
(86, 38, 'Arrival', 400.00, 'Arrival, hotel check-in and local exploration.', 'img/images/sydney_coastal_escape_1.jpg', '2026-01-20', 1),
(87, 38, 'Adventure', 340.00, 'Full-day adventure and cultural activities.', 'img/images/sydney_coastal_escape_2.jpg', '2026-01-21', 2),
(88, 38, 'Sightseeing', 340.00, 'Guided sightseeing tour of major attractions.', 'img/images/sydney_coastal_escape_3.jpg', '2026-01-22', 3),
(89, 38, 'Leisure', 530.00, 'Relaxation, shopping and leisure activities.', 'img/images/sydney_coastal_escape_4.jpg', '2026-01-23', 4),
(90, 39, 'Arrival', 340.00, 'Arrival, hotel check-in and local exploration.', 'img/images/iceland_northern_lights_1.jpg', '2025-12-18', 1),
(91, 39, 'Adventure', 340.00, 'Full-day adventure and cultural activities.', 'img/images/iceland_northern_lights_2.jpg', '2025-12-19', 2),
(92, 39, 'Sightseeing', 250.00, 'Guided sightseeing tour of major attractions.', 'img/images/iceland_northern_lights_3.jpg', '2025-12-20', 3),
(93, 39, 'Leisure', 400.00, 'Relaxation, shopping and leisure activities.', 'img/images/iceland_northern_lights_4.jpg', '2025-12-21', 4),
(94, 40, 'Arrival', 250.00, 'Arrival, hotel check-in and local exploration.', 'img/images/singapore_city_fun_1.jpg', '2025-10-05', 1),
(95, 40, 'Adventure', 340.00, 'Full-day adventure and cultural activities.', 'img/images/singapore_city_fun_2.jpg', '2025-10-06', 2),
(96, 40, 'Sightseeing', 400.00, 'Guided sightseeing tour of major attractions.', 'img/images/singapore_city_fun_3.jpg', '2025-10-07', 3),
(97, 40, 'Leisure', 400.00, 'Relaxation, shopping and leisure activities.', 'img/images/singapore_city_fun_4.jpg', '2025-10-08', 4),
(98, 41, 'Arrival', 250.00, 'Arrival, hotel check-in and local exploration.', 'img/images/hawaii_tropical_holiday_1.jpg', '2026-02-15', 1),
(99, 41, 'Adventure', 340.00, 'Full-day adventure and cultural activities.', 'img/images/hawaii_tropical_holiday_2.jpg', '2026-02-16', 2),
(100, 41, 'Sightseeing', 250.00, 'Guided sightseeing tour of major attractions.', 'img/images/hawaii_tropical_holiday_3.jpg', '2026-02-17', 3),
(101, 41, 'Leisure', 250.00, 'Relaxation, shopping and leisure activities.', 'img/images/hawaii_tropical_holiday_4.jpg', '2026-02-18', 4),
(102, 42, 'Arrival', 250.00, 'Arrival, hotel check-in and local exploration.', 'img/images/canada_rockies_road_trip_1.jpg', '2025-09-25', 1),
(103, 42, 'Adventure', 250.00, 'Full-day adventure and cultural activities.', 'img/images/canada_rockies_road_trip_2.jpg', '2025-09-26', 2),
(104, 42, 'Sightseeing', 340.00, 'Guided sightseeing tour of major attractions.', 'img/images/canada_rockies_road_trip_3.jpg', '2025-09-27', 3),
(105, 42, 'Leisure', 530.00, 'Relaxation, shopping and leisure activities.', 'img/images/canada_rockies_road_trip_4.jpg', '2025-09-28', 4),
(106, 43, 'Arrival', 400.00, 'Arrival, hotel check-in and local exploration.', 'img/images/machu_picchu_expedition_1.jpg', '2026-05-01', 1),
(107, 43, 'Adventure', 400.00, 'Full-day adventure and cultural activities.', 'img/images/machu_picchu_expedition_2.jpg', '2026-05-02', 2),
(108, 43, 'Sightseeing', 400.00, 'Guided sightseeing tour of major attractions.', 'img/images/machu_picchu_expedition_3.jpg', '2026-05-03', 3),
(109, 43, 'Leisure', 530.00, 'Relaxation, shopping and leisure activities.', 'img/images/machu_picchu_expedition_4.jpg', '2026-05-04', 4),
(110, 44, 'Arrival', 250.00, 'Arrival, hotel check-in and local exploration.', 'img/images/vienna_classical_journey_1.jpg', '2025-11-12', 1),
(111, 44, 'Adventure', 250.00, 'Full-day adventure and cultural activities.', 'img/images/vienna_classical_journey_2.jpg', '2025-11-13', 2),
(112, 44, 'Sightseeing', 400.00, 'Guided sightseeing tour of major attractions.', 'img/images/vienna_classical_journey_3.jpg', '2025-11-14', 3),
(113, 44, 'Leisure', 400.00, 'Relaxation, shopping and leisure activities.', 'img/images/vienna_classical_journey_4.jpg', '2025-11-15', 4),
(114, 45, 'Arrival', 250.00, 'Arrival, hotel check-in and local exploration.', 'img/images/norway_fjord_cruise_1.jpg', '2026-06-15', 1),
(115, 45, 'Adventure', 250.00, 'Full-day adventure and cultural activities.', 'img/images/norway_fjord_cruise_2.jpg', '2026-06-16', 2),
(116, 45, 'Sightseeing', 530.00, 'Guided sightseeing tour of major attractions.', 'img/images/norway_fjord_cruise_3.jpg', '2026-06-17', 3),
(117, 45, 'Leisure', 400.00, 'Relaxation, shopping and leisure activities.', 'img/images/norway_fjord_cruise_4.jpg', '2026-06-18', 4),
(118, 46, 'Arrival', 400.00, 'Arrival, hotel check-in and local exploration.', 'img/images/beijing_great_wall_tour_1.jpg', '2025-10-28', 1),
(119, 46, 'Adventure', 340.00, 'Full-day adventure and cultural activities.', 'img/images/beijing_great_wall_tour_2.jpg', '2025-10-29', 2),
(120, 46, 'Sightseeing', 340.00, 'Guided sightseeing tour of major attractions.', 'img/images/beijing_great_wall_tour_3.jpg', '2025-10-30', 3),
(121, 46, 'Leisure', 250.00, 'Relaxation, shopping and leisure activities.', 'img/images/beijing_great_wall_tour_4.jpg', '2025-10-31', 4),
(122, 47, 'Arrival', 530.00, 'Arrival, hotel check-in and local exploration.', 'img/images/morocco_desert_escape_1.jpg', '2026-02-05', 1),
(123, 47, 'Adventure', 400.00, 'Full-day adventure and cultural activities.', 'img/images/morocco_desert_escape_2.jpg', '2026-02-06', 2),
(124, 47, 'Sightseeing', 340.00, 'Guided sightseeing tour of major attractions.', 'img/images/morocco_desert_escape_3.jpg', '2026-02-07', 3),
(125, 47, 'Leisure', 250.00, 'Relaxation, shopping and leisure activities.', 'img/images/morocco_desert_escape_4.jpg', '2026-02-08', 4),
(126, 48, 'Arrival', 250.00, 'Arrival, hotel check-in and local exploration.', 'img/images/vietnam_cultural_journey_1.jpg', '2025-09-18', 1),
(127, 48, 'Adventure', 340.00, 'Full-day adventure and cultural activities.', 'img/images/vietnam_cultural_journey_2.jpg', '2025-09-19', 2),
(128, 48, 'Sightseeing', 400.00, 'Guided sightseeing tour of major attractions.', 'img/images/vietnam_cultural_journey_3.jpg', '2025-09-20', 3),
(129, 48, 'Leisure', 530.00, 'Relaxation, shopping and leisure activities.', 'img/images/vietnam_cultural_journey_4.jpg', '2025-09-21', 4),
(130, 49, 'Arrival', 400.00, 'Arrival, hotel check-in and local exploration.', 'img/images/los_angeles_city_vibes_1.jpg', '2026-03-05', 1),
(131, 49, 'Adventure', 340.00, 'Full-day adventure and cultural activities.', 'img/images/los_angeles_city_vibes_2.jpg', '2026-03-06', 2),
(132, 49, 'Sightseeing', 340.00, 'Guided sightseeing tour of major attractions.', 'img/images/los_angeles_city_vibes_3.jpg', '2026-03-07', 3),
(133, 49, 'Leisure', 250.00, 'Relaxation, shopping and leisure activities.', 'img/images/los_angeles_city_vibes_4.jpg', '2026-03-08', 4),
(134, 50, 'Arrival', 250.00, 'Arrival, hotel check-in and local exploration.', 'img/images/amsterdam_canal_tour_1.jpg', '2025-12-02', 1),
(135, 50, 'Adventure', 400.00, 'Full-day adventure and cultural activities.', 'img/images/amsterdam_canal_tour_2.jpg', '2025-12-03', 2),
(136, 50, 'Sightseeing', 340.00, 'Guided sightseeing tour of major attractions.', 'img/images/amsterdam_canal_tour_3.jpg', '2025-12-04', 3),
(137, 50, 'Leisure', 340.00, 'Relaxation, shopping and leisure activities.', 'img/images/amsterdam_canal_tour_4.jpg', '2025-12-05', 4),
(138, 51, 'Arrival', 340.00, 'Arrival, hotel check-in and local exploration.', 'img/images/dubai_shopping_festival_1.jpg', '2026-01-05', 1),
(139, 51, 'Adventure', 340.00, 'Full-day adventure and cultural activities.', 'img/images/dubai_shopping_festival_2.jpg', '2026-01-06', 2),
(140, 51, 'Sightseeing', 250.00, 'Guided sightseeing tour of major attractions.', 'img/images/dubai_shopping_festival_3.jpg', '2026-01-07', 3),
(141, 51, 'Leisure', 340.00, 'Relaxation, shopping and leisure activities.', 'img/images/dubai_shopping_festival_4.jpg', '2026-01-08', 4),
(142, 52, 'Arrival', 400.00, 'Arrival in Dubai, private transfer to luxury hotel.', 'img/images/dubai_arrival.jpg', '2025-10-12', 1),
(143, 52, 'Luxury Shopping', 530.00, 'Full day shopping at Dubai Mall and Mall of Emirates.', 'img/images/dubai_shopping.jpg', '2025-10-13', 2),
(144, 52, 'Fine Dining & Sightseeing', 400.00, 'Lunch at Burj Al Arab, visit to Burj Khalifa sky lounge.', 'img/images/dubai_dining.jpg', '2025-10-14', 3),
(145, 52, 'Beach & Relaxation', 400.00, 'Relax at Jumeirah Beach, spa treatment and final shopping.', 'img/images/dubai_beach.jpg', '2025-10-15', 4),
(148, 64, 'Flight', 340.00, 'this is my arrival day', 'img/images/1756301072_Screenshot (14).png', '2025-08-28', 1),
(149, 64, 'adventure', 350.00, 'this is my adventure day', 'img/images/1756301256_Screenshot (15).png', '2025-08-29', 2);

-- --------------------------------------------------------

--
-- Table structure for table `trips`
--

CREATE TABLE `trips` (
  `id` int(11) NOT NULL,
  `trip_name` varchar(100) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT 'default.jpg',
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `budget` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trips`
--

INSERT INTO `trips` (`id`, `trip_name`, `destination`, `image`, `description`, `start_date`, `end_date`, `budget`, `created_at`, `user_id`) VALUES
(22, 'Dubai Vacation', 'Dubai, UAE', 'img/dubai1.jpg', 'Visit Dubai and explore Burj Khalifa, beaches, and shopping.', '2025-09-10', '2025-09-20', 2500.00, '2025-08-15 15:39:37', 0),
(23, 'Northern Areas Tour', 'Hunza & Skardu, Pakistan', 'img/hunza.jpg', 'Scenic beauty, mountains, and fresh air in Hunza Valley.', '2025-08-30', '2025-09-05', 1600.00, '2025-08-15 15:39:37', 0),
(24, 'Turkey Adventure', 'Istanbul & Cappadocia', 'img/turkey.jpg', 'Explore the magic of Istanbul and Cappadocia with hot air balloons.', '2025-10-15', '2025-10-25', 3000.00, '2025-08-15 15:39:37', 0),
(25, 'Bali Beach Getaway', 'Bali, Indonesia', 'img/bali.jpg', 'Relax on Bali’s beaches and explore ancient temples.', '2025-11-05', '2025-11-12', 1850.00, '2025-08-15 15:39:37', 0),
(26, 'Swiss Alps Adventure', 'Switzerland', 'img/swiss.jpg', 'Experience skiing and breathtaking mountain views.', '2026-01-15', '2026-01-20', 1200.00, '2025-08-15 15:39:37', 0),
(27, 'Paris Romantic Escape', 'Paris, France', 'img/paris.jpg', 'Enjoy the Eiffel Tower, Seine River, and Parisian cafes.', '2025-12-10', '2025-12-15', 2000.00, '2025-08-15 15:39:37', 0),
(28, 'Maldives Luxury Retreat', 'Maldives', 'img/maldives.jpg', 'Stay in overwater villas and enjoy crystal-clear waters.', '2025-09-05', '2025-09-12', 3500.00, '2025-08-15 15:39:37', 0),
(29, 'Thailand Island Hopping', 'Phuket & Krabi, Thailand', 'img/thailand.jpg', 'Explore tropical islands, beaches, and vibrant nightlife.', '2025-10-02', '2025-10-10', 1500.00, '2025-08-15 15:39:37', 0),
(30, 'New York City Lights', 'New York, USA', 'img/newyork.jpg', 'Visit Times Square, Central Park, and iconic landmarks.', '2026-03-10', '2026-03-17', 2500.00, '2025-08-15 15:39:37', 0),
(31, 'Rome Historical Tour', 'Rome, Italy', 'img/rome.jpg', 'Walk through ancient history and taste authentic Italian cuisine.', '2025-11-18', '2025-11-24', 1800.00, '2025-08-15 15:39:37', 0),
(32, 'Santorini Sunset Views', 'Santorini, Greece', 'img/santorini.jpg', 'Famous blue-domed buildings and magical sunsets.', '2026-04-05', '2026-04-12', 2200.00, '2025-08-15 15:39:37', 0),
(33, 'London City Break', 'London, UK', 'img/london.jpg', 'Explore Buckingham Palace, Big Ben, and the Thames.', '2025-10-22', '2025-10-28', 2100.00, '2025-08-15 15:39:37', 0),
(34, 'Japan Cherry Blossom Tour', 'Tokyo & Kyoto, Japan', 'img/japan.jpg', 'Witness cherry blossoms and traditional temples.', '2026-03-25', '2026-04-02', 3200.00, '2025-08-15 15:39:37', 0),
(35, 'Egypt Pyramids Adventure', 'Cairo & Giza, Egypt', 'img/egypt.jpg', 'Discover ancient pyramids and the Nile River.', '2025-09-15', '2025-09-22', 1700.00, '2025-08-15 15:39:37', 0),
(36, 'South Africa Safari', 'Kruger National Park, South Africa', 'img/safari.jpg', 'Wildlife safari and breathtaking landscapes.', '2025-08-30', '2025-09-06', 2800.00, '2025-08-15 15:39:37', 0),
(37, 'Barcelona Culture Trip', 'Barcelona, Spain', 'img/barcelona.jpg', 'Gaudí architecture, beaches, and vibrant streets.', '2025-11-08', '2025-11-14', 1900.00, '2025-08-15 15:39:37', 0),
(38, 'Sydney Coastal Escape', 'Sydney, Australia', 'img/sydney.jpg', 'Opera House, beaches, and coastal adventures.', '2026-01-20', '2026-01-28', 4000.00, '2025-08-15 15:39:37', 0),
(39, 'Iceland Northern Lights', 'Reykjavik, Iceland', 'img/iceland.jpg', 'Chase the Northern Lights and explore glaciers.', '2025-12-18', '2025-12-25', 4500.00, '2025-08-15 15:39:37', 0),
(40, 'Singapore City Fun', 'Singapore', 'img/singapore.jpg', 'Gardens by the Bay, Marina Bay Sands, and street food.', '2025-10-05', '2025-10-11', 1600.00, '2025-08-15 15:39:37', 0),
(41, 'Hawaii Tropical Holiday', 'Honolulu, Hawaii', 'img/hawaii.jpg', 'Surfing, volcano hikes, and island relaxation.', '2026-02-15', '2026-02-22', 3700.00, '2025-08-15 15:39:37', 0),
(42, 'Canada Rockies Road Trip', 'Banff & Jasper, Canada', 'img/canada.jpg', 'Mountain lakes, hiking, and scenic drives.', '2025-09-25', '2025-10-03', 3000.00, '2025-08-15 15:39:37', 0),
(43, 'Machu Picchu Expedition', 'Cusco, Peru', 'img/machu.jpg', 'Trek to the ancient Inca citadel of Machu Picchu.', '2026-05-01', '2026-05-10', 4200.00, '2025-08-15 15:39:37', 0),
(44, 'Vienna Classical Journey', 'Vienna, Austria', 'img/vienna.jpg', 'Music, museums, and imperial palaces.', '2025-11-12', '2025-11-18', 2300.00, '2025-08-15 15:39:37', 0),
(45, 'Norway Fjord Cruise', 'Bergen, Norway', 'img/norway.jpg', 'Sail through majestic fjords and scenic villages.', '2026-06-15', '2026-06-22', 3800.00, '2025-08-15 15:39:37', 0),
(46, 'Beijing Great Wall Tour', 'Beijing, China', 'img/wall.jpg', 'Walk along the Great Wall and explore historic Beijing.', '2025-10-28', '2025-11-03', 2600.00, '2025-08-15 15:39:37', 0),
(47, 'Morocco Desert Escape', 'Marrakech & Sahara', 'img/morocco.jpg', 'Camel rides and desert camping under the stars.', '2026-02-05', '2026-02-12', 2400.00, '2025-08-15 15:39:37', 0),
(48, 'Vietnam Cultural Journey', 'Hanoi & Ha Long Bay', 'img/vietnam.jpg', 'Cruise in Ha Long Bay and enjoy Vietnamese cuisine.', '2025-09-18', '2025-09-25', 1400.00, '2025-08-15 15:39:37', 0),
(49, 'Los Angeles City Vibes', 'Los Angeles, USA', 'img/la.jpg', 'Hollywood, beaches, and entertainment capital.', '2026-03-05', '2026-03-12', 2800.00, '2025-08-15 15:39:37', 0),
(50, 'Amsterdam Canal Tour', 'Amsterdam, Netherlands', 'img/amsterdam.jpg', 'Bicycles, canals, and Dutch culture.', '2025-12-02', '2025-12-08', 2100.00, '2025-08-15 15:39:37', 0),
(51, 'Dubai Shopping Festival', 'Dubai, UAE', 'img/dubai2.jpg', 'World-class shopping and luxury experiences.', '2026-01-05', '2026-01-10', 2700.00, '2025-08-15 15:39:37', 0),
(52, 'Dubai Luxury Tour', 'Dubai, UAE', 'img/dubai_luxury.jpg', '4 days of luxury shopping, fine dining, and beach resorts in Dubai.', '2025-10-12', '2025-10-16', 1950.00, '2025-08-15 15:43:36', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `remember_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `created_at`, `remember_token`) VALUES
(2, 'Muhammad Fasih', 'ur Rehman', 'fasih2412@gmail.com', '$2y$10$HnvdtC65J3Qx8zh3z/I9fOaL5FlZrdIQiJfnTTGQiSRoi2GXzEP6S', '2025-08-22 16:41:38', NULL),
(3, 'Muhammad', 'Fasih', 'fasih123@gmail.com', '$2y$10$AF.DuhtkgIlJ9jrdVVcJZ.Xn2y6Y52/Tqtyn21uXM7bzZ9S99M3Fa', '2025-08-22 17:16:20', NULL),
(5, 'Muhammad', 'Fasih ur Rehman', 'fasih2561@gmail.com', '$2y$10$jGkU7X9ihXoF39UzsGDXvuZvdwhwB40B6xDi8DbB.tUucYhi8I42.', '2025-08-22 17:27:29', 'ee500676878f2cf67fb6894c58879258');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_details`
--
ALTER TABLE `blog_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `blog_id` (`blog_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `trip_id` (`trip_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trip_id` (`trip_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `expenses_templates`
--
ALTER TABLE `expenses_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `blog_details`
--
ALTER TABLE `blog_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `expenses_templates`
--
ALTER TABLE `expenses_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;

--
-- AUTO_INCREMENT for table `trips`
--
ALTER TABLE `trips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blog_details`
--
ALTER TABLE `blog_details`
  ADD CONSTRAINT `blog_details_ibfk_1` FOREIGN KEY (`blog_id`) REFERENCES `blogs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_trip_id` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
