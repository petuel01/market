-- Table to track swap requests between users
CREATE TABLE IF NOT EXISTS swap_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    requester_id INT NOT NULL,
    owner_id INT NOT NULL,
    requester_listing_id INT NOT NULL,
    owner_listing_id INT NOT NULL,
    status ENUM('pending','accepted','declined') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (requester_listing_id) REFERENCES listings(id) ON DELETE CASCADE,
    FOREIGN KEY (owner_listing_id) REFERENCES listings(id) ON DELETE CASCADE
);
