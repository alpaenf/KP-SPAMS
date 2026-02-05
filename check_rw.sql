SELECT wilayah, COUNT(*) as total 
FROM pelanggan 
GROUP BY wilayah 
ORDER BY wilayah;

SELECT rw, COUNT(*) as total 
FROM pelanggan 
GROUP BY rw 
ORDER BY rw;

SELECT DISTINCT rw 
FROM pelanggan 
WHERE rw IS NOT NULL AND rw != '' 
ORDER BY rw;
