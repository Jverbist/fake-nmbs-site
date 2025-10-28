-- 01-create-users.sql

-- ensure the table exists
CREATE TABLE IF NOT EXISTS users (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  email    VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255)       NOT NULL
);

-- seed your test accounts
INSERT INTO users (email, password) VALUES
  ('freddie@cyberhell.be','Eminem4Ever'),
  ('mcurie@nmbs.be','RadiumRocks!'),
  ('aeinstein@hotmail.com','Relativity4Evr'),
  ('bart@cyberhell.be','SlimShady2025$!'),
  ('conductor@rails.be','AllAboard8508'),
  ('ticketmaster@rails.be','TicketsPlease5068'),
  ('rfermi@nmbs.be','AtomicMan#5'),
  ('trainspotter@rails.be','RailFan2024!'),
  ('sfranklin@hotmail.com','DNAGenius@2024'),
  ('engineer@rails.be','EnginePower2024!')
ON DUPLICATE KEY UPDATE email = email;
