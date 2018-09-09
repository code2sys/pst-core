Linking Major Unit Types
========================

Major units exposes the key from the motorcycle_type table in the URL to filter by ATV, UTV, etc.

Here's what that table normally looks like:

<pre>
MariaDB [ss_v1]> select * from motorcycle_type;
+----+-----------------+------------------+----------+---------+
| id | name            | crs_machine_type | crs_type | offroad |
+----+-----------------+------------------+----------+---------+
|  1 | ATV             | ATV              | ATV      |       0 |
|  2 | UTV             | UTV              | UTV      |       0 |
|  3 | Street Bike     | MOT              | MOT      |       0 |
|  4 | Off-Road        | MOT              | MOT      |       1 |
|  5 | Water Craft     | WAT              | WAT      |       0 |
|  6 | Snowmobile      | SNO              | SNO      |       0 |
|  7 | Utility         | NULL             | NULL     |       0 |
|  8 | Trailer         | NULL             | NULL     |       0 |
|  9 | Car             | NULL             | NULL     |       0 |
| 10 | Truck           | NULL             | NULL     |       0 |
| 11 | Boat            | NULL             | NULL     |       0 |
| 12 | Lawn and Garden | NULL             | NULL     |       0 |
| 13 | Scooter         | NULL             | NULL     |       0 |
| 14 | RUV             | NULL             | NULL     |       0 |
| 15 | Generators      | NULL             | NULL     |       0 |
| 16 | Go Kart         | NULL             | NULL     |       0 |
| 17 | Golf Cart       | NULL             | NULL     |       0 |
+----+-----------------+------------------+----------+---------+
17 rows in set (0.00 sec)

</pre>

The numbers are pretty stable. The trick to the URL is that the filter relies on the session, so you have to reset it manually with a query string parameter:

<pre>
/Motorcycle_List?fltr=new&vehicles=1&filterChange=1&search_keywords=
</pre>

Would get you new ATVs, and it would reset any other filters.