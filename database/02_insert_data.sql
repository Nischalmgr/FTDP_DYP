USE fashionhub;

INSERT INTO design_types (name, description) VALUES
('Fashion Design', 'General fashion design'),
('Streetwear', 'Streetwear collections'),
('Luxury Fashion', 'Luxury fashion collections'),
('Footwear Design', 'Footwear and sneakers'),
('Accessories Design', 'Accessories and jewellery'),
('Textile Design', 'Textile and fabric design'),
('Pattern Design', 'Pattern making'),
('Graphic Design', 'Fashion graphics'),
('3D Fashion', '3D visualization'),
('Sustainable Fashion', 'Eco-friendly fashion');

INSERT INTO users (full_name, email, password, role, is_verified, created_at) VALUES
('Admin', 'admin@ftdp.com', '$2y$10$rX7Lr1LVP7edtcC2dCpE4OW2hUJiJQncuIsjeAWI9l3RDIC/aQ94W', 'admin', 'Y', '2026-09-01 09:00:00'),
('Sarah Khan', 'sarah.khan@ftdp.com', '$2y$10$rX7Lr1LVP7edtcC2dCpE4OW2hUJiJQncuIsjeAWI9l3RDIC/aQ94W', 'designer', 'Y', '2026-09-03 10:15:00'),
('Liam Carter', 'liam.carter@ftdp.com', '$2y$10$rX7Lr1LVP7edtcC2dCpE4OW2hUJiJQncuIsjeAWI9l3RDIC/aQ94W', 'designer', 'Y', '2026-09-04 14:30:00'),
('Aiko Tanaka', 'aiko.tanaka@ftdp.com', '$2y$10$rX7Lr1LVP7edtcC2dCpE4OW2hUJiJQncuIsjeAWI9l3RDIC/aQ94W', 'designer', 'Y', '2026-09-06 08:45:00'),
('Maria Lopez', 'maria.lopez@ftdp.com', '$2y$10$rX7Lr1LVP7edtcC2dCpE4OW2hUJiJQncuIsjeAWI9l3RDIC/aQ94W', 'designer', 'Y', '2026-09-08 16:20:00'),
('Omar Hassan', 'omar.hassan@ftdp.com', '$2y$10$rX7Lr1LVP7edtcC2dCpE4OW2hUJiJQncuIsjeAWI9l3RDIC/aQ94W', 'designer', 'Y', '2026-09-10 11:05:00'),
('Priya Sharma', 'priya.sharma@ftdp.com', '$2y$10$rX7Lr1LVP7edtcC2dCpE4OW2hUJiJQncuIsjeAWI9l3RDIC/aQ94W', 'designer', 'Y', '2026-09-12 13:40:00'),
('Emma Wilson', 'emma.wilson@urbanthread.com', '$2y$10$rX7Lr1LVP7edtcC2dCpE4OW2hUJiJQncuIsjeAWI9l3RDIC/aQ94W', 'company', 'Y', '2026-09-05 09:30:00'),
('Daniel Brooks', 'daniel.brooks@maisonvelour.com', '$2y$10$rX7Lr1LVP7edtcC2dCpE4OW2hUJiJQncuIsjeAWI9l3RDIC/aQ94W', 'company', 'Y', '2026-09-07 12:10:00'),
('Noor Ahmed', 'noor.ahmed@greenloom.com', '$2y$10$rX7Lr1LVP7edtcC2dCpE4OW2hUJiJQncuIsjeAWI9l3RDIC/aQ94W', 'company', 'Y', '2026-09-09 15:00:00'),
('Lucas Meyer', 'lucas.meyer@stridelab.com', '$2y$10$rX7Lr1LVP7edtcC2dCpE4OW2hUJiJQncuIsjeAWI9l3RDIC/aQ94W', 'company', 'Y', '2026-09-11 10:25:00');

INSERT INTO designers (user_id, headline, bio, location, website, portfolio_link, experience_years) VALUES
(2, 'Womenswear and tailoring designer', 'I design modern womenswear with a focus on clean tailoring and natural fabrics. Five years in small studios and two seasons with a Milan label.', 'London, UK', 'https://sarahkhan.design', 'https://behance.net/sarahkhan', 5),
(3, 'Streetwear and graphic designer', 'Streetwear designer who loves oversized shapes, bold graphics and limited drops. Previously worked on three capsule collections for independent brands.', 'Manchester, UK', 'https://liamcarter.co', 'https://instagram.com/liamcarter.design', 4),
(4, 'Textile and pattern designer', 'I create hand-drawn prints and woven textile patterns inspired by Japanese craft. Open to freelance print work and fabric development.', 'Tokyo, Japan', 'https://aikotanaka.jp', 'https://behance.net/aikotanaka', 6),
(5, 'Footwear and accessories designer', 'Footwear designer with experience in sneaker prototyping and leather accessories. I enjoy turning rough sketches into wearable products.', 'Madrid, Spain', 'https://marialopez.studio', 'https://dribbble.com/marialopez', 7),
(6, '3D fashion and visualization artist', 'I build digital garments and runway visuals in 3D so brands can review collections before sampling. Focused on cutting waste in the sample stage.', 'Dubai, UAE', 'https://omarhassan.art', 'https://behance.net/omarhassan', 3),
(7, 'Sustainable fashion designer', 'Zero-waste pattern cutter working with deadstock and recycled fibres. I help small brands plan collections that use less material.', 'Leeds, UK', 'https://priyasharma.eco', 'https://behance.net/priyasharma', 2);

INSERT INTO designer_specializations (designer_id, specialization_name) VALUES
(1, 'Womenswear'),
(1, 'Tailoring'),
(1, 'Luxury Fashion'),
(2, 'Streetwear'),
(2, 'Graphic Design'),
(3, 'Textile Design'),
(3, 'Pattern Design'),
(3, 'Print Design'),
(4, 'Footwear Design'),
(4, 'Leather Accessories'),
(5, '3D Fashion'),
(5, 'Virtual Sampling'),
(6, 'Sustainable Fashion'),
(6, 'Zero-Waste Patterns');

INSERT INTO companies (user_id, company_name, industry, website, company_size, location, description) VALUES
(8, 'Urban Thread', 'Streetwear', 'https://urbanthread.com', '11-50', 'Manchester, UK', 'Urban Thread is an independent streetwear label releasing small seasonal drops. We work closely with freelance designers and are always looking for fresh graphic ideas.'),
(9, 'Maison Velour', 'Luxury Fashion', 'https://maisonvelour.com', '51-200', 'Paris, France', 'Maison Velour is a luxury womenswear house known for tailoring and fine fabrics. Our studio is growing and hiring designers who care about craft.'),
(10, 'GreenLoom', 'Sustainable Fashion', 'https://greenloom.com', '1-10', 'Leeds, UK', 'GreenLoom makes everyday clothing from recycled and deadstock fabrics. We are a small team that values low-waste design.'),
(11, 'StrideLab', 'Footwear', 'https://stridelab.com', '51-200', 'Berlin, Germany', 'StrideLab designs performance and lifestyle sneakers. Our product team works with designers across Europe on new silhouettes.');

INSERT INTO designs (user_id, title, description, image, created_at) VALUES
(2, 'Structured Wool Coat', 'A double-breasted wool coat with sharp shoulders and a relaxed hem, designed for autumn city wear.', 'uploads/designs/seed_design_1.jpg', '2026-09-15 10:00:00'),
(2, 'Silk Evening Dress', 'Bias-cut silk dress with an open back and a soft cowl neckline.', 'uploads/designs/seed_design_2.jpg', '2026-09-22 18:30:00'),
(3, 'Oversized Graphic Hoodie', 'Heavyweight hoodie with a large back print and dropped shoulders. Part of the Night Shift capsule.', 'uploads/designs/seed_design_3.jpg', '2026-09-16 12:15:00'),
(3, 'Cargo Utility Set', 'Matching cargo jacket and trousers with removable pockets and reflective trims.', 'uploads/designs/seed_design_4.jpg', '2026-09-25 09:40:00'),
(4, 'Indigo Wave Print', 'A repeat print inspired by traditional wave motifs, drawn by hand and cleaned digitally.', 'uploads/designs/seed_design_5.jpg', '2026-09-18 08:20:00'),
(4, 'Woven Jacquard Study', 'Jacquard weave pattern study in three colourways for outerwear and accessories.', 'uploads/designs/seed_design_6.jpg', '2026-09-28 14:10:00'),
(5, 'Low-Top Court Sneaker', 'A clean leather low-top sneaker with a stitched toe cap and recycled rubber sole.', 'uploads/designs/seed_design_7.jpg', '2026-09-19 11:55:00'),
(5, 'Leather Crossbody Bag', 'Compact crossbody bag with a magnetic flap and adjustable braided strap.', 'uploads/designs/seed_design_8.jpg', '2026-09-30 16:05:00'),
(6, 'Digital Runway Look 01', 'A fully 3D garment shown on a virtual runway, built for early collection review.', 'uploads/designs/seed_design_9.jpg', '2026-09-20 13:25:00'),
(6, 'Virtual Puffer Jacket', '3D simulation of a quilted puffer jacket with realistic fabric drape and seams.', 'uploads/designs/seed_design_10.jpg', '2026-10-02 10:45:00'),
(7, 'Zero-Waste Wrap Skirt', 'A wrap skirt cut from a single rectangle of fabric so nothing goes to waste.', 'uploads/designs/seed_design_11.jpg', '2026-09-21 15:35:00'),
(7, 'Deadstock Patchwork Shirt', 'Relaxed shirt pieced together from deadstock cotton offcuts in a calm colour palette.', 'uploads/designs/seed_design_12.jpg', '2026-10-04 09:10:00');

INSERT INTO design_type_mapping (design_id, design_type_id) VALUES
(1, 1), (1, 3),
(2, 1), (2, 3),
(3, 2), (3, 8),
(4, 2), (4, 1),
(5, 6), (5, 7),
(6, 6), (6, 7),
(7, 4), (7, 10),
(8, 5), (8, 3),
(9, 9), (9, 1),
(10, 9), (10, 2),
(11, 10), (11, 7),
(12, 10), (12, 6);

INSERT INTO design_likes (design_id, user_id, created_at) VALUES
(1, 3, '2026-09-16 09:00:00'),
(1, 8, '2026-09-17 11:20:00'),
(1, 9, '2026-09-18 14:45:00'),
(2, 9, '2026-09-23 10:10:00'),
(2, 4, '2026-09-23 19:30:00'),
(3, 2, '2026-09-17 08:15:00'),
(3, 8, '2026-09-17 12:40:00'),
(3, 11, '2026-09-19 16:00:00'),
(4, 8, '2026-09-26 10:30:00'),
(4, 5, '2026-09-26 13:50:00'),
(5, 2, '2026-09-19 09:25:00'),
(5, 10, '2026-09-20 15:15:00'),
(6, 9, '2026-09-29 11:00:00'),
(7, 11, '2026-09-20 10:05:00'),
(7, 3, '2026-09-21 17:45:00'),
(7, 10, '2026-09-22 09:35:00'),
(8, 9, '2026-10-01 12:20:00'),
(9, 11, '2026-09-21 14:10:00'),
(9, 7, '2026-09-22 08:50:00'),
(10, 8, '2026-10-03 10:40:00'),
(11, 10, '2026-09-22 11:30:00'),
(11, 2, '2026-09-23 15:55:00'),
(12, 10, '2026-10-05 09:20:00'),
(12, 4, '2026-10-05 13:05:00');

INSERT INTO design_comments (design_id, user_id, comment, created_at) VALUES
(1, 9, 'The shoulder line on this coat is beautiful. Would love to see it in a darker camel tone.', '2026-09-18 15:00:00'),
(1, 3, 'Great proportions. The hem length works really well.', '2026-09-16 09:10:00'),
(3, 8, 'This graphic placement is exactly the direction we want for our winter drop.', '2026-09-17 12:50:00'),
(5, 2, 'Lovely rhythm in the repeat. Did you draw this on paper first?', '2026-09-19 09:40:00'),
(5, 4, 'Yes, ink on paper first and cleaned up in Illustrator afterwards.', '2026-09-19 10:05:00'),
(7, 11, 'Clean silhouette. Could this work with a thicker sole for our trail range?', '2026-09-20 10:20:00'),
(9, 7, 'Really helpful for planning a collection before sampling. Nice work.', '2026-09-22 09:00:00'),
(11, 10, 'This is the kind of thinking we need at GreenLoom. Great pattern layout.', '2026-09-22 11:45:00'),
(12, 4, 'The colour palette feels calm and balanced.', '2026-10-05 13:15:00'),
(8, 9, 'The strap detail is a nice touch.', '2026-10-01 12:30:00');

INSERT INTO jobs (company_id, title, description, location, salary, employment_type, deadline, status, created_at) VALUES
(1, 'Graphic Designer for Streetwear Drops', 'We are looking for a graphic designer to create prints and artwork for our seasonal streetwear drops. You will work with our in-house team and see your designs go into production.', 'Manchester, UK', '£32,000 - £38,000', 'Full-Time', '2027-01-31', 'open', '2026-09-26 10:00:00'),
(1, 'Freelance Illustrator', 'Short project to illustrate a set of limited edition tote bags and posters for our spring launch.', 'Remote', '£300 per illustration', 'Freelance', '2026-12-20', 'open', '2026-10-01 11:30:00'),
(2, 'Junior Womenswear Designer', 'Join our design studio to support the womenswear team with sketches, tech packs and fabric research. Experience with tailoring is a plus.', 'Paris, France', '€34,000 - €40,000', 'Full-Time', '2027-02-15', 'open', '2026-09-29 09:15:00'),
(2, 'Pattern Cutting Intern', 'A six month internship in our pattern room. You will learn how luxury garments are drafted, cut and fitted.', 'Paris, France', '€1,200 per month', 'Internship', '2027-01-10', 'open', '2026-10-03 14:20:00'),
(3, 'Sustainable Materials Designer', 'Help us source and test recycled and deadstock fabrics, and plan collections that waste less material.', 'Leeds, UK', '£30,000 - £36,000', 'Full-Time', '2027-01-20', 'open', '2026-09-30 16:40:00'),
(3, 'Part-Time Zero-Waste Pattern Cutter', 'Part-time role creating zero-waste patterns for our core range of shirts, skirts and trousers.', 'Leeds, UK', '£18 per hour', 'Part-Time', '2026-12-31', 'open', '2026-10-05 10:10:00'),
(4, 'Footwear Concept Designer', 'Design new sneaker silhouettes from first sketch to 3D render and prototype. You will work closely with our product and engineering teams.', 'Berlin, Germany', '€45,000 - €55,000', 'Full-Time', '2027-03-01', 'open', '2026-10-02 08:50:00'),
(4, '3D Visualization Contractor', 'Contract role creating 3D renders of upcoming sneaker releases for our design review meetings.', 'Remote', '€350 per day', 'Contract', '2026-11-30', 'closed', '2026-09-22 13:00:00');

INSERT INTO job_applications (job_id, designer_id, cover_letter, resume, application_status, applied_at) VALUES
(1, 2, 'I have spent four years designing graphics for streetwear capsules and would love to bring that experience to Urban Thread.', NULL, 'shortlisted', '2026-09-28 10:30:00'),
(2, 3, 'My hand-drawn style would suit your tote and poster project well. I have attached links to similar illustration work in my portfolio.', NULL, 'pending', '2026-10-03 09:00:00'),
(3, 1, 'I would be glad to support your womenswear team. My tailoring background and Milan studio experience match this role closely.', NULL, 'reviewed', '2026-10-01 11:45:00'),
(4, 6, 'I am keen to build on my zero-waste cutting skills inside a professional pattern room.', NULL, 'pending', '2026-10-06 15:25:00'),
(5, 6, 'Sustainable materials and low-waste planning are the core of my work. I would love to help GreenLoom grow.', NULL, 'hired', '2026-10-02 12:10:00'),
(6, 6, 'I can start part-time immediately and have cut several zero-waste patterns for small brands.', NULL, 'pending', '2026-10-07 09:35:00'),
(7, 4, 'I have prototyped sneakers for three years and would enjoy working on new silhouettes with your team.', NULL, 'shortlisted', '2026-10-04 14:00:00'),
(7, 5, 'My 3D workflow can speed up your concept reviews and reduce the number of physical samples needed.', NULL, 'rejected', '2026-10-05 10:50:00'),
(8, 5, 'I build digital footwear and garments daily and could start as soon as the contract opens.', NULL, 'reviewed', '2026-09-24 16:15:00');

INSERT INTO saved_designs (user_id, design_id, saved_at) VALUES
(8, 3, '2026-09-17 13:00:00'),
(8, 4, '2026-09-26 10:45:00'),
(9, 1, '2026-09-18 15:10:00'),
(9, 2, '2026-09-23 10:20:00'),
(10, 11, '2026-09-22 11:50:00'),
(10, 12, '2026-10-05 09:30:00'),
(11, 7, '2026-09-20 10:30:00'),
(11, 9, '2026-09-21 14:20:00'),
(2, 5, '2026-09-19 09:50:00'),
(3, 7, '2026-09-21 18:00:00');

INSERT INTO saved_jobs (user_id, job_id, saved_at) VALUES
(2, 1, '2026-09-27 09:00:00'),
(2, 3, '2026-09-30 10:15:00'),
(3, 2, '2026-10-02 12:00:00'),
(4, 5, '2026-10-01 14:30:00'),
(5, 7, '2026-10-03 11:10:00'),
(6, 4, '2026-10-04 16:45:00'),
(6, 5, '2026-10-01 09:20:00'),
(7, 6, '2026-10-06 13:35:00');

INSERT INTO messages (sender_id, receiver_id, message, is_read, sent_at) VALUES
(8, 3, 'Hi Liam, we really liked your hoodie design. Are you open to freelance graphic work this winter?', 'Y', '2026-09-18 10:00:00'),
(3, 8, 'Thanks Emma! Yes, I have space from November. What kind of project do you have in mind?', 'Y', '2026-09-18 11:20:00'),
(8, 3, 'A set of four back prints for our winter drop. I will send a short brief this week.', 'Y', '2026-09-18 11:45:00'),
(9, 2, 'Hello Sarah, your wool coat caught our attention. Would you share your availability for a short call?', 'Y', '2026-09-19 09:30:00'),
(2, 9, 'Hello Daniel, thank you. I am free on Thursday or Friday afternoon.', 'N', '2026-09-19 12:10:00'),
(10, 7, 'Priya, your zero-waste skirt is exactly what we are building towards. Can we talk about a role?', 'Y', '2026-09-23 10:05:00'),
(7, 10, 'Absolutely, I would love to hear more about the team and the materials you work with.', 'Y', '2026-09-23 11:30:00'),
(11, 5, 'Hi Maria, we are hiring a footwear concept designer. Your sneaker would fit our range nicely.', 'N', '2026-10-03 09:40:00'),
(4, 2, 'Sarah, your silk dress is stunning. Would you be interested in a joint print and tailoring project?', 'Y', '2026-09-24 15:20:00'),
(2, 4, 'Thank you Aiko! I would like that. Let us share some mood boards first.', 'Y', '2026-09-24 16:00:00'),
(6, 5, 'Maria, I can render your sneaker in 3D if you would like a quick visual for your portfolio.', 'N', '2026-10-04 12:25:00'),
(5, 6, 'That would be great Omar, I will send you the files tomorrow.', 'N', '2026-10-04 14:10:00');

INSERT INTO notifications (user_id, type, title, message, link, is_read, created_at) VALUES
(2, 'announcement', 'Welcome to FTDP', 'Welcome to the Fashion Talent Discovery Platform. Complete your profile and upload your first design.', NULL, 'Y', '2026-09-03 10:20:00'),
(3, 'announcement', 'Welcome to FTDP', 'Welcome to the Fashion Talent Discovery Platform. Complete your profile and upload your first design.', NULL, 'Y', '2026-09-04 14:35:00'),
(8, 'announcement', 'Welcome to FTDP', 'Welcome to the Fashion Talent Discovery Platform. Post a job and discover designers.', NULL, 'Y', '2026-09-05 09:35:00'),
(2, 'announcement', 'New feature: comments', 'You can now comment on designs from the Explore page.', NULL, 'N', '2026-10-06 09:00:00'),
(3, 'announcement', 'New feature: comments', 'You can now comment on designs from the Explore page.', NULL, 'N', '2026-10-06 09:00:00'),
(8, 'announcement', 'New feature: comments', 'You can now comment on designs from the Explore page.', NULL, 'N', '2026-10-06 09:00:00'),
(2, 'like', 'New like on your design', 'Daniel Brooks liked your design "Structured Wool Coat".', 'my_designs.php', 'N', '2026-09-18 14:45:00'),
(3, 'like', 'New like on your design', 'Emma Wilson liked your design "Oversized Graphic Hoodie".', 'my_designs.php', 'Y', '2026-09-17 12:40:00'),
(6, 'like', 'New like on your design', 'Lucas Meyer liked your design "Digital Runway Look 01".', 'my_designs.php', 'N', '2026-09-21 14:10:00'),
(7, 'like', 'New like on your design', 'Noor Ahmed liked your design "Deadstock Patchwork Shirt".', 'my_designs.php', 'N', '2026-10-05 09:20:00'),
(2, 'comment', 'New comment on your design', 'Daniel Brooks commented on "Structured Wool Coat": The shoulder line on this coat is beautiful.', 'my_designs.php', 'N', '2026-09-18 15:00:00'),
(5, 'comment', 'New comment on your design', 'Lucas Meyer commented on "Low-Top Court Sneaker": Clean silhouette.', 'my_designs.php', 'Y', '2026-09-20 10:20:00'),
(7, 'comment', 'New comment on your design', 'Noor Ahmed commented on "Zero-Waste Wrap Skirt": This is the kind of thinking we need at GreenLoom.', 'my_designs.php', 'N', '2026-09-22 11:45:00'),
(8, 'application', 'New application received', 'Liam Carter applied for "Graphic Designer for Streetwear Drops".', 'applicants.php?job=1', 'Y', '2026-09-28 10:30:00'),
(9, 'application', 'New application received', 'Sarah Khan applied for "Junior Womenswear Designer".', 'applicants.php?job=3', 'N', '2026-10-01 11:45:00'),
(10, 'application', 'New application received', 'Priya Sharma applied for "Sustainable Materials Designer".', 'applicants.php?job=5', 'Y', '2026-10-02 12:10:00'),
(11, 'application', 'New application received', 'Maria Lopez applied for "Footwear Concept Designer".', 'applicants.php?job=7', 'N', '2026-10-04 14:00:00'),
(3, 'application', 'You have been shortlisted', 'Good news! You have been shortlisted for "Graphic Designer for Streetwear Drops" at Urban Thread.', 'my_jobs.php', 'N', '2026-09-30 09:00:00'),
(7, 'application', 'Congratulations, you were hired', 'Great news! You were hired for "Sustainable Materials Designer" at GreenLoom.', 'my_jobs.php', 'N', '2026-10-08 10:00:00'),
(5, 'application', 'Application update', 'Your application for "Footwear Concept Designer" at StrideLab was not selected this time.', 'my_jobs.php', 'Y', '2026-10-07 11:00:00'),
(3, 'message', 'New message from Emma Wilson', 'Hi Liam, we really liked your hoodie design.', 'messages.php?with=8', 'Y', '2026-09-18 10:00:00'),
(2, 'message', 'New message from Daniel Brooks', 'Hello Sarah, your wool coat caught our attention.', 'messages.php?with=9', 'Y', '2026-09-19 09:30:00'),
(9, 'message', 'New message from Sarah Khan', 'Hello Daniel, thank you. I am free on Thursday or Friday afternoon.', 'messages.php?with=2', 'N', '2026-09-19 12:10:00'),
(5, 'message', 'New message from Lucas Meyer', 'Hi Maria, we are hiring a footwear concept designer.', 'messages.php?with=11', 'N', '2026-10-03 09:40:00'),
(5, 'message', 'New message from Omar Hassan', 'Maria, I can render your sneaker in 3D if you would like a quick visual.', 'messages.php?with=6', 'N', '2026-10-04 12:25:00'),
(6, 'message', 'New message from Maria Lopez', 'That would be great Omar, I will send you the files tomorrow.', 'messages.php?with=5', 'N', '2026-10-04 14:10:00'),
(5, 'removal', 'Your design was removed', 'Your design "Old Sketch" was removed by an admin. Reason: Duplicate upload.', NULL, 'Y', '2026-09-27 08:30:00');
