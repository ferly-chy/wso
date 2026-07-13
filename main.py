with open("mari.php", "rb") as src:
    php_code = src.read()

with open("output.php", "wb") as dst:
    dst.write(b"\xFF\xD8\xFF\xE0")
    dst.write(php_code)
