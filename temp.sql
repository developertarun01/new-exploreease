SELECT S.SNAME, C.CNAME
FROM STUDENT S
INNER JOIN COURSE C
ON S.CID =C.CID;

SELECT C.CNAME
FROM COURSE C
LEFT JOIN STUDENT S
ON S.CID = C.CID
WHERE S.CID IS NULL;

SELECT MAX(S.MARKS), C.CNAME
FROM STUDENT S
INNER JOIN COURSE C 
ON S.CID = C.CID 
GROUP BY C.CNAME;

-- GROUP BY - Used to group rows having similar values so aggregate functions (SUM, AVG, MAX, MIN, COUNT) can be applied.
-- ORDER BY - Used to sort the result in ascending (ASC) or descending (DESC) order.