declare
   s2 number;
 begin
   INSERT INTO drivers (name) VALUES ('atilla') returning driverId into s2;
   dbms_output.put_line(s2);
 end;
 /