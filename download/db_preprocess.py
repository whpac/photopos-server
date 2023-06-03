from math import floor

with open('places.txt', 'r', encoding='utf-8') as fin:
    with open('db_data.csv', 'w', encoding='utf-8') as fout:
        for line in fin:
            fields = line.strip().split('\t')
            if len(fields) < 6:
                continue

            wiki = '"' + fields[0] + '"'
            qId = '"' + fields[1] + '"'
            latitude = float(fields[2])
            longitude = float(fields[3])
            label = '"' + fields[4] + '"'
            description = '"' + fields[5] + '"'

            if qId == '"null"':
                qId = 'NULL'
            if label == '"null"':
                label = wiki
                if '(' in label:
                    label = label[:label.index('(')]
            if description == '"null"':
                description = 'NULL'

            tileLat = floor(latitude)
            tileLng = floor(longitude)

            fout.write(f'NULL,{latitude},{longitude},{label},{description},{wiki},{qId},{tileLat},{tileLng}\n')