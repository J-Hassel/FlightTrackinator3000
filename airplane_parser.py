import pymysql

connection = pymysql.connect(host="localhost", user="root", passwd="", database="flighttrackinator3000")
cursor = connection.cursor()

cursor.execute("DELETE FROM airplane")

for line in open("airplanes.dat", encoding="utf8"):
    name = line.split(',')[0][1:-1]
    iataCode = line.split(',')[2][1:-1]

    if iataCode == "":
        continue

    sql = "INSERT INTO airplane(name, iataCode, capacity) VALUES (%s, %s, %s);"
    cursor.execute(sql,(name, iataCode, 0))

    connection.commit()
connection.close()

