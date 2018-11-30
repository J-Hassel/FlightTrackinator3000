import pymysql

connection = pymysql.connect(host="localhost", user="root", passwd="", database="flighttrackinator3000")
cursor = connection.cursor()

cursor.execute("DELETE FROM airline")

for line in open("airlines.dat", encoding="utf8"):
    name = line.split(',')[1][1:-1]
    iataCode = line.split(',')[3][1:-1]
    country = line.split(',')[6][1:-1]
    operational = line.split(',')[7][1:-2]

    if iataCode == "" or country == "" or operational == "N":
        continue

    sql = "INSERT INTO airline(name, iataCode, country) VALUES (%s, %s, %s);"
    cursor.execute(sql,(name, iataCode, country))

    connection.commit()
connection.close()
