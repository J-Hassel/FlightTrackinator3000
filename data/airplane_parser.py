import pymysql

connection = pymysql.connect(host="localhost", user="root", passwd="", database="flighttrackinator3000")
cursor = connection.cursor()

cursor.execute("DELETE FROM airplane")

for line in open("airplanes.dat", encoding="utf8"):
    name = line.split(',')[0][1:-1]
    iataCode = line.split(',')[1][1:-1]
    icaoCode = line.split(',')[2][1:-2]

    if icaoCode == "":
        continue

    sql = "INSERT INTO airplane(name, iataCode, icaoCode, capacity) VALUES (%s, %s, %s, %s);"
    cursor.execute(sql,(name, iataCode, icaoCode, 0))

    connection.commit()
connection.close()
