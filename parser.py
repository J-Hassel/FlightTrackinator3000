import pymysql
import json
import requests

response = requests.get("http://aviation-edge.com/v2/public/flights?key=7d8f71-1d01ce&depIata=TLH")
data = json.loads(response.text)
connection = pymysql.connect(host="localhost", user="root", passwd="", database="test")
cursor = connection.cursor()

cursor.execute("DELETE FROM flight")

for flight in data:
    if flight['status'] == "en-route":
        print("Airline:", flight['airline']['iataCode'], "- Flight:", flight['flight']['number'], "::", flight['departure']['iataCode'], "->", flight['arrival']['iataCode'])
        print("GEO: ", flight['geography'])
        print("SPD: ", flight['speed'])

        lat = flight['geography']['latitude']
        lon = flight['geography']['latitude']
        direction = flight['geography']['direction']
        altitude = flight['geography']['altitude']
        print("INSERTING TO DB: ", lat, lon, direction, altitude, '\n')

        sql = "INSERT INTO flight(lat, lon, direction, altitude) VALUES (%s, %s, %s, %s);"
        cursor.execute(sql, (lat, lon, direction, altitude))
        connection.commit()

connection.close()






    # for adsbexchange API
    # if(flight['Id'] == 8190193):
    #     print(flight["Alt"])
#API KEY FOR Aviation Edge: 7d8f71-1d01ce