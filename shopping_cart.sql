CREATE TABLE member_shopping_cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    product_id INT NOT NULL,
    suffix CHAR(3) NOT NULL, -- 新增的字段，设置为 NOT NULL
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES ministore_members(id),
);

CREATE TABLE visitor_shopping_cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visitor_id VARCHAR(255) NOT NULL,
    product_id INT NOT NULL,
    suffix CHAR(3) NOT NULL, -- 新增的字段，设置为 NOT NULL
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
);
